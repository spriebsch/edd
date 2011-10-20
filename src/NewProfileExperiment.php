<?php
/**
 * Copyright (c) 2009-2011 Stefan Priebsch <stefan@priebsch.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Stefan Priebsch nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    edd
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @license    BSD License
 */

namespace spriebsch\edd;

use spriebsch\factory\MasterFactoryInterface;
use spriebsch\factory\ChildFactoryInterface;

abstract class Experiment
{
    protected $name;
    protected $sessionId;
    protected $environment;
    protected $isStarted;
    protected $runExperiment = FALSE;
    protected $masterFactory;

    public function __construct($sessionId, Environment $environment)
    {
        $this->sessionId = $sessionId;
        $this->environment = $environment;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function isRunning()
    {
        return $this->runExperiment;
    }

    public function start()
    {
        $this->decide();
        $this->isStarted = TRUE;
    }    

    public function run(ApplicationFactory $factory)
    {
        if (!$this->isStarted) {
            $this->start();
        }
    
        if ($this->runExperiment) {
            $factory->register($this);
        }
    }

    abstract protected function end(Logger $logger);
    abstract protected function decide();    
}

class NewProfileExperiment extends Experiment implements ChildFactoryInterface
{
    protected $name = 'NewProfile';

    protected $language;
    protected $userSignUpDate;

    protected $languageIsGerman;
    protected $signedUpMoreThanOneYearAgo;

    protected $rating;
    
    public function getInstanceFor($type)
    {
        switch ($type) {
            case 'ProfilePage':
                return new NewProfilePage();
        }    
    }
    
    public function getTypes()
    {
        return array('ProfilePage');
    }

    public function setMaster(MasterFactoryInterface $factory)
    {
        $this->masterFactory = $factory;
    }
    
    public function setRating($rating)
    {
        $this->rating = $rating;
    }
    
    public function end(Logger $logger)
    {
        $message =  $this->name . ' Experiment for session ' . $this->sessionId . PHP_EOL . PHP_EOL;

        $message .= 'Criteria:' . PHP_EOL;
        $message .= '- Session language must be DE' . PHP_EOL;
        $message .= '- User must have signed up more than one year ago' . PHP_EOL . PHP_EOL;


        $message .= 'Session Language: ' . $this->language . '    -> ';
        $message .= ($this->languageIsGerman ? 'qualifies' : 'does not qualify') . PHP_EOL;

        if ($this->languageIsGerman == 'DE') {
            $message .= 'User Signup: ' . $this->userSignupDate->format('Y-m-d') . ' -> ';
            $message .= ($this->signedUpMoreThanOneYearAgo ? 'qualifies' : 'does not qualify') . PHP_EOL;
        }
    
        $message .= PHP_EOL;
    
        if ($this->runExperiment) {
            $message .= '-> showing new profile page' . PHP_EOL . PHP_EOL;

            $message .= 'Rating: ' . str_repeat('*', $this->rating) . str_repeat(' ', 5 - $this->rating) . ' (' . $this->rating . ')' . PHP_EOL;
        } else {
            $message .= '-> showing old profile page' . PHP_EOL;
        }
    
        $logger->log($message, $this->rating);
    }

    protected function decide()
    {
        $this->language = $this->environment->getLanguage();
    
        $this->languageIsGerman = $this->language == 'DE';

        $now = new \DateTime('now');
        $this->userSignupDate = $this->environment->getUser()->getSignupDate();

        $this->signedUpMoreThanOneYearAgo = $now->diff($this->userSignupDate)->y > 1;
        
        $this->runExperiment = $this->languageIsGerman && $this->signedUpMoreThanOneYearAgo;
    }
}
