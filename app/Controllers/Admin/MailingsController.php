<?php

namespace App\Controllers\Admin;

use Symfony\Component\HttpFoundation\Request;

use App\Models\Mailing;
use App\Models\Subscriber;

class MailingsController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        if($this->role == 'user' || $this->role == 'moderator'){
            $this->echoForbittenMessage();
        }
    }

    public function index()
    {

        $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;

        $offset = $page === 1 ? 0 : ($page - 1) * $this->limit;

        $limit = " LIMIT ".$offset.','.$this->limit;

        $sort = '';

        $data = [
        "endQuery" => " GROUP BY mailings.id ".$sort.$limit
        ];
        
        $mailing = new Mailing();

        $res = [
        'data' =>
            $mailing->getTemplates(
            ['mailings.id', 'mailings.title', 'mailings.body'
            ],
            $data
        ),
        'total' => $mailing->count('id', [])
        ];

        echo json_encode($res);
        die();
    }

    public function create()
    {
        $mailing = new Mailing();
        $data = [
            'mailings.title' => $this->sanitizeText($_POST['title']),
            'mailings.body' => $_POST['body'],
        ];
        
        $res = $mailing->createTemplate($data);

        echo json_encode($res);
        die();
    }

    public function update()
    {
        $mailing = new Mailing();
        $data = [
            'id' => (int) $_POST['id'],
            'title' => $this->sanitizeText($_POST['title']),
            'body' => $_POST['body'],
        ];
        
        $res = $mailing->updateTemplate($data);

        echo json_encode($res);
        die();
    }

    public function subscribers_total()
    {
        $subscriber = new Subscriber();
        $total = (int) $subscriber->count('id', []);
        echo json_encode([
            'total' => $total
        ]);
        die();
    }

    public function sendEmail()
    {
        $id_template = intval($_POST['id']);
        $offset =  isset($_POST['offset']) ? (int) $_POST['offset'] : 0;

        $subscriber = new Subscriber();
        
        $subscriber_current = $subscriber->getSubscribers(
            [
                'subscribers.id','subscribers.email',
            ],
            [
                "endQuery" => " LIMIT ".$offset .", 1"
            ]
        );

        if( !$subscriber_current){
            echo json_encode([
                'success' => false
            ]);
            die();
        }

        $mailing = new Mailing();
        $template = $mailing->getTemplate(
            [
                'mailings.id', 'mailings.title', 'mailings.body'
            ],
            [
                'mailings.id' => $id_template,
            ]
        );
        
        if($template){

            $msg = $this->twig->render('emails/newsletter.twig', [
                'title' => $template['title'],
                'body' => $template['body'],
            ]);
    
            $res = $this->sendMail([
                'theme' => $template['title'],
                'email' => $subscriber_current[0]['email'],
                'name' => '',
                'msg' =>  $msg
            ]);
            if($res > 0){
                echo json_encode([
                    'success' => true,
                    'email' => $subscriber_current[0]['email']
                ]);
                die();
            }

        }
        echo json_encode([
            'success' => false
        ]);
        die();
    }

    public function delete(Request $request)
    {
        $id = (int) $request->attributes->get('id');

        $mailing = new Mailing();

        $data = [
        'mailings.id' => $id,
        ];

        $res = $mailing->deleteTemplate($data);
        echo json_encode(['res'=>$res]);
        die();
        
    }
 
}
