document.addEventListener('DOMContentLoaded', async function () {
    try {
        const map = L.map('map').setView([49.8175, 15.4730], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors',
        }).addTo(map);

        const response = await fetch('get-events.php');
        if (!response.ok) {
            throw new Error('Chyba při načítání dat z databáze.');
        }

        const events = await response.json();
        if (!Array.isArray(events)) {
            throw new Error('Očekáván seznam událostí, ale obdrženo něco jiného.');
        }

        const eventsList = document.getElementById('events-list');

        events.forEach((event) => {
            if (!event.date || !event.name || event.latitude === undefined || event.longitude === undefined) {
                console.warn('Přeskočeno neplatné události:', event);
                return;
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${new Date(event.date).toLocaleString()}</td>
                <td>${event.name}</td>
                <td>${event.type === 'official' ? event.field_name : 'Neoficiální'}</td>
                <td>${event.description || 'Bez popisu'}</td>
                <td id="attendees-${event.id}">${event.attendees || 0}</td>
                <td><button class="btn btn-primary" id="btn-${event.id}" onclick="toggleSignup(${event.id}, '${event.attendees > 0 ? 'cancel' : 'signup'}')">${event.attendees > 0 ? 'Odhlásit' : 'Přihlásit'}</button></td>
            `;
            eventsList.appendChild(row);

            const marker = L.marker([event.latitude, event.longitude]).addTo(map);
            marker.bindPopup(`
                <strong>${event.name}</strong><br>
                Datum: ${new Date(event.date).toLocaleString()}<br>
                Typ: ${event.type === 'official' ? 'Oficiální' : 'Neoficiální'}<br>
                Počet přihlášených: ${event.attendees || 0}
            `);
        });
    } catch (error) {
        console.error('Došlo k chybě:', error);
        alert('Nepodařilo se načíst data. Zkuste to prosím znovu.');
    }
});

function toggleSignup(eventId, action) {
    fetch('signup-event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `event_id=${eventId}&action=${action}`
    })
    .then(response => response.text())
    .then(data => {
        const attendeesCell = document.getElementById(`attendees-${eventId}`);
        const button = document.getElementById(`btn-${eventId}`);

        if (data === "error_signup" || data === "error_cancel") {
            alert('Došlo k chybě při přihlašování nebo odhlašování.');
        } else if (data === "already_signed_up") {
            alert('Již jste přihlášeni na tuto akci.');
        } else {
            // Aktualizujeme počet přihlášených uživatelů
            attendeesCell.innerText = data;

            // Změníme text tlačítka
            if (action === 'signup') {
                button.innerText = 'Odhlásit';
                button.setAttribute('onclick', `toggleSignup(${eventId}, 'cancel')`);
            } else {
                button.innerText = 'Přihlásit';
                button.setAttribute('onclick', `toggleSignup(${eventId}, 'signup')`);
            }
        }
    })
    .catch(error => {
        console.error('Chyba při komunikaci se serverem:', error);
        alert('Došlo k chybě při přihlašování nebo odhlašování.');
    });
}
