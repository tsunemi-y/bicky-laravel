<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Consts\ConstUser;
use Illuminate\Http\Request;
use App\Services\MailService;
use App\Services\UserService;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct(
        private MailService $mailService, 
        private UserService $userService
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

        // メールデータ作成
        $date = date('Ymd');
        $viewFile = 'admin.emails.receipt';
        $subject = '領収書のご送付';
        $attachFile = "app/領収書_{$date}.pdf";

        // 領収書を出力し、ストレージに配置
        $pdf = \PDF::loadView('admin/emails/receiptPdf', $args);
        $downloadedPdf = $pdf->output();
        file_put_contents(storage_path("app/領収書_{$date}.pdf"), $downloadedPdf);

        // 領収書送信
        $this->mailService->sendMailToUser($args, $viewFile, $subject, $attachFile);

        // 領収書削除
        unlink(storage_path("app/領収書_{$date}.pdf"));
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
        $fileName = $request->file('file')->getClientOriginalName();
        $request->file('file')->storeAs('', $fileName);

        // パラメータ設定
        $args = [
            "name"  => $request->name,
            "email" => $request->email,
        ];

        $attachFile = "app/{$fileName}";

        // メールデータ作成
        $viewFile = 'admin.emails.evaluation';
        $subject = '評価表のご送付';

        // 評価表送信
        $this->mailService->sendMailToUser($args, $viewFile, $subject, $attachFile);

        // 評価表削除
        unlink(storage_path("app/{$fileName}"));
    }
}
