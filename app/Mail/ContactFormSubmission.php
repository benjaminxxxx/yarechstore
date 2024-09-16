<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Storage;

class ContactFormSubmission extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $files = [];

    /**
     * Create a new message instance.
     *
     * @param array $data Datos del cliente y mensaje.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->files = $data['attachments']; // Documentos seleccionados (ej. XML, CDR, etc.)
    }

    /**
     * Define el asunto del correo.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Documentos Solicitados - YARECH',
        );
    }

    /**
     * Define el contenido del correo.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoices', // Vista del email
        );
    }

    /**
     * Define los archivos adjuntos.
     *
     * @return array Archivos adjuntos
     */
    public function attachments(): array
    {
        return $this->files;
    }
}
