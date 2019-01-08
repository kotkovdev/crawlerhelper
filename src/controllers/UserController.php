<?php
namespace App\Controllers;

use \App\Models\User;

class UserController
{
    private $container;
    public function __construct()
    {
        global $container;
        $this->container = $container;
    }

    public function __invoke($req, $res)
    {
        global $container;
        $container->view->render($res, 'login.twig');
    }

    public function forgot($req, $res)
    {
        $data = $req->getParsedBody();
        if (isset($_POST['email'])) {
            $this->forgotPassword($data['email']);
        } else {
            if (isset($_GET['hash'])) {
                $this->resetPassword($req, $res);
            }
            global $container;
            $container->view->render($res, 'forgot.twig');
        }
    }

    private function resetPassword($req, $res)
    {
        global $container;
        $data = $_GET;
        $data['validator'] = null;
        if (isset($_POST['hash'])) {
            $data = $_POST;
            if ($_POST['password'] == $_POST['confirm_password']) {
                $this->container->get('db');
                $user = User::where('reset_hash', '=', $data['hash'])->first();
                if ($user) {
                    $user->password = sha1($_POST['password']);
                    $user->reset_hash = '';
                    $user->save();
                    echo 'New password is changed. Go to <a href="/login">Login</a>';
                    die;
                    //return $res->withRedirect('/login');
                } else {
                    $data['validator'] = 'Invalid hash';
                }

            } else {
                $data['validator'] = 'Not valid password';
            }
        }
        $container->view->render($res, 'reset.twig', $data);
    }

    private function forgotPassword(String $email)
    {
        $this->container->get('db');
        $user = User::where('email', '=', $email)->first();
        if ($user) {
            $hash = sha1(time().$email);
            $user->reset_hash = $hash;
            $user->save();
            $this->sendHashKey($email, $hash);
            echo 'Check your email address. You can close this tab.';
            return;
        }
    }

    private function sendHashKey($email, $hash)
    {
        $link = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/forgot?hash=' . $hash;
        $template = '
            <h3>Restore password</h3>
            <p>For restore password you must go to <a href="' . $link . '" target="_blank">' . $link . '</a></p>
        ';
        mail($email, 'Restoring password for CrawlerHelper', $template);
    }

    public function login($req, $res)
    {
        $this->container->get('db');
        $data = $req->getParsedBody();
        $user = User::where('email', '=', $data['email'])->where('password', '=', sha1($data['password']))->first();
        if ($user) {
            $_SESSION['id'] = $user->toArray()['id'];
            return $res->withRedirect('/');
        } else {
            global $container;
            $data['validator'] = 'Wrong email or password';
            $container->view->render($res, 'login.twig', $data);
        }
    }
}