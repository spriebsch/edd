<?php // @startCodeCoverageIgnore
spl_autoload_register(
   function($class) {
      static $classes = null;
      if ($classes === null) {
         $classes = array(
            'spriebsch\\edd\\applicationfactory' => '/ApplicationFactory.php',
            'spriebsch\\edd\\environment' => '/Environment.php',
            'spriebsch\\edd\\exception' => '/Exception.php',
            'spriebsch\\edd\\logger' => '/Logger.php',
            'spriebsch\\edd\\newprofileexperiment' => '/NewProfileExperiment.php',
            'spriebsch\\edd\\newprofilepage' => '/NewProfilePage.php',
            'spriebsch\\edd\\pagefactory' => '/PageFactory.php',
            'spriebsch\\edd\\profilepage' => '/ProfilePage.php',
            'spriebsch\\edd\\session' => '/Session.php',
            'spriebsch\\edd\\user' => '/User.php'
          );
      }
      $cn = strtolower($class);
      if (isset($classes[$cn])) {
         require __DIR__ . $classes[$cn];
      }
   }
);
