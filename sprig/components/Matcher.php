<?php
namespace sprig\components;

use Craft;
use craft\elements\Entry;
use craft\elements\User;
use putyourlightson\sprig\base\Component;

class Matcher extends Component
{
    protected ?string $_template = '_sprig/components/matcher';
    public int $affection;
    public int $activityLevel;
    public int $bodySize;
    public int $hairyness;
    public int $diet;
    public int $attractiveness;

    public ?Entry $match = null;
    public int $lowestScore = 999;
    public array $pawmates = [];


    public function init(): void
    {
        parent::init();
        
        // Get the current logged-in user
        $currentUser = Craft::$app->getUser()->getIdentity();
        
        // Load user's profile attributes if logged in, otherwise use defaults
        if ($currentUser) {
            $this->affection = $this->affection ?? $currentUser->affection ?? 5;
            $this->activityLevel = $this->activityLevel ?? $currentUser->activityLevel ?? 5;
            $this->bodySize = $this->bodySize ?? $currentUser->bodySize ?? 5;
            $this->hairyness = $this->hairyness ?? $currentUser->hairyness ?? 5;
            $this->diet = $this->diet ?? $currentUser->diet ?? 5;
            $this->attractiveness = $this->attractiveness ?? $currentUser->attractiveness ?? 5;
        }
        $this->pawmates = Entry::find()->section('pawmates')->all();
    }

    public function findMatch(): Entry
    {
        return $this->_calculateMatch();
    }

    public function render(): string
    {
        // Add artificial delay to show loading indicator
        sleep(2);
        
        $this->match = $this->findMatch();
        return parent::render();
    }

    private function _calculateMatch(): Entry
    {
       $match = null;
       $lowestScore = 999;

        // calculate the score for each pawmate as a potential match
       foreach ($this->pawmates as $pawmate) {
           $score = 0;
           $score += abs($this->affection - $pawmate->affection);
           $score += abs($this->activityLevel - $pawmate->activityLevel);
           $score += abs($this->bodySize - $pawmate->bodySize);
           $score += abs($this->hairyness - $pawmate->hairyness);
           $score += abs($this->diet - $pawmate->diet);
           $score += abs($this->attractiveness - $pawmate->attractiveness);

           // new lowestScore means more similar; set as match
           if ($score < $lowestScore) {
               $lowestScore = $score;
               $match = $pawmate;
           }
       }

       $this->lowestScore = $lowestScore;
       return $match;
    }
}