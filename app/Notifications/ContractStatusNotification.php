<?php

namespace App\Notifications;

use App\Mail\ContratNotifMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client as TwilioClient;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class ContractStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $contrat;
    protected $event;

    public function __construct($contrat, string $event)
    {
        $this->contrat = $contrat;
        $this->event = $event;
    }

    /**
     * Determine which channels to use for the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        $contact = $this->contrat->prestation->contact_artiste;

        // Check if contact is an email address
        if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
            Log::info('Notification channel selected: mail', [
                'contrat_id' => $this->contrat->id,
                'contact' => $contact,
            ]);
            return ['mail'];
        }

        // Validate phone number for WhatsApp/SMS
        if ($this->validatePhoneNumber($contact)) {
            Log::info('Notification channel selected: whatsapp with sms fallback', [
                'contrat_id' => $this->contrat->id,
                'contact' => $contact,
            ]);
            return ['whatsapp', 'sms'];
        }

        // Fallback to mail if phone number is invalid
        Log::warning('Falling back to mail due to invalid phone number', [
            'contrat_id' => $this->contrat->id,
            'contact' => $contact,
        ]);
        return ['mail'];
    }

    /**
     * Validate and format a phone number.
     *
     * @param string $phone
     * @return bool|string E.164 formatted number if valid, false otherwise
     */
    protected function validatePhoneNumber(string $phone): bool|string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $number = $phoneUtil->parse($phone, 'FR'); // Adjust region code as needed
            if ($phoneUtil->isValidNumber($number)) {
                return $phoneUtil->format($number, PhoneNumberFormat::E164);
            }
            Log::warning('Invalid phone number', [
                'contrat_id' => $this->contrat->id,
                'phone' => $phone,
            ]);
            return false;
        } catch (NumberParseException $e) {
            Log::warning('Phone number parsing failed', [
                'contrat_id' => $this->contrat->id,
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get the notification message for the given event and channel.
     *
     * @param string $channel
     * @return string
     */
    protected function getMessage(string $channel): string
    {
        $statusMessages = [
            'envelope-sent' => [
                'mail' => 'Le contrat a été livré aux destinataires. Vous serez informé lorsqu\'ils les auront consultés.',
                'whatsapp' => "Contrat #{$this->contrat->id} livré aux destinataires.",
                'sms' => "Contrat #{$this->contrat->id} livré aux destinataires.",
            ],
            'envelope-delivered' => [
                'mail' => 'Le contrat a été  consulté par les destinataires. Il ne reste que leurs signatures.',
                'whatsapp' => "Contrat #{$this->contrat->id}  consulté par les destinataires.",
                'sms' => "Contrat #{$this->contrat->id}  consulté par les destinataires.",
            ],
            'envelope-completed' => [
                'mail' => 'Le contrat a été signé par toutes les parties.',
                'whatsapp' => "Contrat #{$this->contrat->id} signé par toutes les parties.",
                'sms' => "Contrat #{$this->contrat->id} signé par toutes les parties.",
            ],
            'envelope-declined' => [
                'mail' => 'Le contrat a été refusé.',
                'whatsapp' => "Contrat #{$this->contrat->id} refusé.",
                'sms' => "Contrat #{$this->contrat->id} refusé.",
            ],
            'envelope-voided' => [
                'mail' => 'Le contrat a été annulé.',
                'whatsapp' => "Contrat #{$this->contrat->id} annulé.",
                'sms' => "Contrat #{$this->contrat->id} annulé.",
            ],
        ];

        return (string) $statusMessages[$this->event][$channel] ?? "Contrat #{$this->contrat->id} mis à jour.";
    }


    public function toMail(mixed $notifiable): \Illuminate\Mail\Mailable
    {
        $notificationMessage = $this->getMessage('mail'); // Renamed
        $subject = "Des nouvelles sur le contrat de {$this->contrat->prestation->artiste->nom} ({$this->contrat->prestation->lieu_prestation})";

        return (new ContratNotifMail($this->contrat, $subject, $notificationMessage))
            ->to($this->contrat->prestation->contact_artiste);
    }

    /**
     * Get the WhatsApp representation of the notification.
     *
     * @param mixed $notifiable
     * @return string
     * @throws \Exception
     */
    public function toWhatsApp( mixed $notifiable): string
    {
        $message = $this->getMessage('whatsapp');
        $contact = $notifiable->routeNotificationForWhatsApp();

        // Validate and format phone number
        $formattedContact = $this->validatePhoneNumber($contact);
        if (!$formattedContact) {
            throw new \Exception('Invalid phone number for WhatsApp');
        }

        // Send WhatsApp message via Twilio
        $twilio = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));
        $response = $twilio->messages->create(
            "whatsapp:{$formattedContact}",
            [
                'from' => 'whatsapp:' . config('services.twilio.whatsapp_from'),
                'body' => $message,
            ]
        );

        Log::info('WhatsApp notification sent', [
            'contrat_id' => $this->contrat->id,
            'contact' => $formattedContact,
            'message' => $message,
            'twilio_sid' => $response->sid,
        ]);

        return $message;
    }

    /**
     * Get the SMS representation of the notification.
     *
     * @param mixed $notifiable
     * @return string
     */
    public function toSms( mixed $notifiable): string
    {
        $message = $this->getMessage('sms');
        $contact = $notifiable->routeNotificationForSms();

        // Validate and format phone number
        $formattedContact = $this->validatePhoneNumber($contact);
        if (!$formattedContact) {
            Log::error('Invalid phone number for SMS; notification not sent', [
                'contrat_id' => $this->contrat->id,
                'contact' => $contact,
            ]);
            return $message;
        }

        // Send SMS via Twilio
        try {
            $twilio = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));
            $response = $twilio->messages->create(
                $formattedContact,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message,
                ]
            );
            Log::info('SMS notification sent', [
                'contrat_id' => $this->contrat->id,
                'contact' => $formattedContact,
                'message' => $message,
                'twilio_sid' => $response->sid,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS notification', [
                'contrat_id' => $this->contrat->id,
                'contact' => $formattedContact,
                'error' => $e->getMessage(),
            ]);
        }

        return $message;
    }
}
