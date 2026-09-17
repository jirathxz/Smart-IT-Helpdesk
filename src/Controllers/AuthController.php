<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Validator;
use App\Repositories\UserRepository;

/**
 * Authentication Controller (Developer 2)
 */
class AuthController extends Controller
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->render('auth/login', ['title' => 'เข้าสู่ระบบ - Smart IT Helpdesk'], 'auth');
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'email'    => 'required|email',
            'password' => 'required',
        ])) {
            $this->redirect('/login', null, $validator->firstError());
        }

        if (Auth::login($email, $password)) {
            $user = Auth::user();
            $this->redirect('/', "ยินดีต้อนรับคุณ {$user['name']} เข้าสู่ระบบ!");
        }

        $this->redirect('/login', null, 'อีเมลหรือรหัสผ่านไม่ถูกต้อง');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->render('auth/register', ['title' => 'ลงทะเบียนผู้ใช้ใหม่ - Smart IT Helpdesk'], 'auth');
    }

    public function register(): void
    {
        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'name'     => 'required|min:3|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ])) {
            $this->redirect('/register', null, $validator->firstError());
        }

        $email = trim($_POST['email']);
        if ($this->userRepo->findByEmail($email)) {
            $this->redirect('/register', null, 'อีเมลนี้ถูกใช้งานในระบบแล้ว');
        }

        $userId = $this->userRepo->create([
            'name'          => trim($_POST['name']),
            'email'         => $email,
            'password_hash' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'role'          => 'user',
            'line_user_id'  => trim($_POST['line_user_id'] ?? '') ?: null,
        ]);

        Auth::loginById($userId);
        $this->redirect('/', 'ลงทะเบียนสำเร็จและเข้าสู่ระบบเรียบร้อยแล้ว');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login', 'ออกจากระบบเรียบร้อยแล้ว');
    }

    /**
     * Quick login switcher for testing and demonstration
     */
    public function quickLogin(int $userId): void
    {
        if (Auth::loginById($userId)) {
            $user = Auth::user();
            $this->redirect('/', "สลับเป็นสิทธิ์ [{$user['role']}] คุณ {$user['name']} เรียบร้อย");
        }
        $this->redirect('/login', null, 'ไม่พบบัญชีผู้ใช้ที่ต้องการ');
    }
}
