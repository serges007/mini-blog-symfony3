<?php

namespace Apps\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppsUserBundle extends Bundle{
    public function getParent(){
      return 'FOSUserBundle';
    }    
}
