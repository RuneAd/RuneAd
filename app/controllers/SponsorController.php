<?php
 class SponsorController extends Controller {

     private static $disabled = true;

     public function index() {
         $packages = SponsorPackages::where('visible', 1)->get();

        if ($this->user) {
            $servers = Servers::getServersByOwner($this->user->user_id);
             $this->set("servers", $servers);
         }

         $this->set("page_disabled", self::$disabled);
         $this->set("packages", $packages);
         return true;
     }

  }
