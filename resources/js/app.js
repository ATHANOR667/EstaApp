import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST || 'ws-eu.pusher.com',
    wsPort: import.meta.env.VITE_PUSHER_PORT || 443,
    wssPort: import.meta.env.VITE_PUSHER_PORT || 443,
    forceTLS: true,
    encrypted: true,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    }
});

document.addEventListener('livewire:init', () => {
    console.log('Livewire initialisé');
    const userId = window.Laravel?.userId;
    console.log('Vérification de userId:', userId);

    if (userId) {
        console.log('Tentative d\'abonnement au canal privé: contrat-form-modal.' + userId);
        const channel = window.Echo.private(`contrat-form-modal.${userId}`);

        channel.subscribed(() => {
            console.log(`Abonnement au canal privé réussi: contrat-form-modal.${userId}`);
        }).error((error) => {
            console.error('Échec de l\'abonnement au canal privé:', {
                message: error.message,
                status: error.status,
                response: error.response
            });
        });

        // Log tous les événements reçus sur le canal
        ['ContractContentGenerated', 'ContractContentGenerationFailed'].forEach(eventName => {
            channel.listen(eventName, (data) => {
                let logMessage = `Événement ${eventName} reçu`;
                if (data) {
                    const keys = Object.keys(data);
                    if (keys.length > 0) {
                        logMessage += ` avec variables: ${keys.map(key => `${key}=${JSON.stringify(data[key])}`).join(', ')}`;
                    } else {
                        logMessage += ' sans variables supplémentaires';
                    }
                } else {
                    logMessage += ' sans données';
                }
                console.log(logMessage, data);
            });
        });
    } else {
        console.error('Erreur : Aucun ID utilisateur trouvé');
    }
});
