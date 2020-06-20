<?php
 class SponsorController extends Controller {

     public function index() {
         $packages = SponsorPackages::where('visible', 1)->get();

         if ($this->user) {
             $servers = Servers::getServersByOwner($this->user->user_id);
             $this->set("servers", $servers);
         }

         $this->set("packages", $packages);
         return true;
     }

 }
