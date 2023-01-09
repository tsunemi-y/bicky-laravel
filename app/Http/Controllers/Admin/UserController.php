<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FileService;
use App\Services\MailService;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private MailService $mailService, 
        private UserService $userService,
        private FileService $fileService,
    )
    {
    }

    public function index(Request $request, User $user)
    {
        return $user
            ->fuzzyName($request->name)
            ->equalId($request->id)
            ->get(['id', 'parentName', 'email', 'fee']);
    }

    public function sendReceipt(Request $request)
    {
        // パラメータ設定
        $args = [
            "name"              => $request->name,
            "email"             => $request->email,
            "fee"               => $request->fee,
        ];

        $date = date('Ymd');
        $viewFile = 'admin.emails.receipt';
        $subject = '領収書のご送付';
        $attachFile = "app/領収書_{$date}.pdf";
        $PDFView = 'admin/emails/receiptPdf';

        $this->fileService->putPDF($args, $PDFView, $attachFile);

        // 領収書送信
        $this->mailService->sendMailToUser($args, $viewFile, $subject, $attachFile);

        // 領収書削除
        $this->fileService->delete($attachFile);
    }

    public function updateFee(Request $request, User $user)
    {
        $user->update([
            'fee'       => $request->fee,
            'use_time'  => $this->userService->getUseTimeByFee($request->fee),
        ]);
    }

    public function sendEvaluation(Request $request)
    {
        // パラメータ設定
        $args = [
            "name"  => $request->name,
            "email" => $request->email,
        ];

        $fileName = $request->file('file')->getClientOriginalName();
        $this->fileService->putRequestedFile($request, $fileName);

        $viewFile = 'admin.emails.evaluation';
        $subject = '評価表のご送付';
        $attachFile = "app/{$fileName}";

        // 評価表送信
        $this->mailService->sendMailToUser($args, $viewFile, $subject, $attachFile);

        // 評価表削除
        $this->fileService->delete($attachFile);
    }
}
