<?php
 use Illuminate\Pagination\Paginator;

 class PaymentsController extends Controller {

     public function index($search = null, $page = 1) {
         Paginator::currentPageResolver(function() use ($page) {
             return $page;
         });

         if ($this->request->hasPost("search")) {
     $search = $this->request->getPost("search", "string");
     $search = str_replace(" ", "-", $search);
     $this->redirect("admin/payments/".$search);
     exit;
 }

         if ($search != null) {
           $payments = Payments::where("username", "LIKE", "%$search%")->paginate(15);
$numRes   = count($payments->items());

if (!$payments || $numRes == 0) {
    $payments = Payments::paginate(15);
    $this->set("error", "Your query returned 0 results. Did you type it correctly?");
} else {
    $this->set("success", "Your query returned {$numRes} results.");
    $this->set("search", $search);
}
} else {
$payments = Payments::paginate(15);
         }

         $this->set("payments", $payments);
         return true;
     }

     public function beforeExecute() {
         return parent::beforeExecute();
     }
 }
