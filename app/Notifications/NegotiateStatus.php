<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NegotiateStatus extends Notification
{
    use Queueable;

    protected $request;
    protected $buyItems;
    protected $seller;

    public function __construct($request, $buyItems, $seller)
    {
        $this->request = $request;
        $this->buyItems = $buyItems;
        $this->seller = $seller;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $msg = '';
        $codeStr = '';
        foreach ($this->buyItems as $value) {
            $codeStr .= "#" . $value->livestock?->code . " ";
        }

        if ($this->request->status == 'DISETUJUI') {
            $msg = 'Pengajuan pembelian anda atas ternak ' . $codeStr . ' telah disetujui oleh penjual ' . $this->seller->fullname;
        } else {
            $msg = 'Pengajuan pembelian anda atas ternak ' . $codeStr . ' telah ditolak oleh penjual ' . $this->seller->fullname;
        }

        return [
            'status' => $this->request->status,
            'text' => $msg,
            'seller_note' => !empty($this->request->note) ? $this->request->note : null
        ];
    }
}
