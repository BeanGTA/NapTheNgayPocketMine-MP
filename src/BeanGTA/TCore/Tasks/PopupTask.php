<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10/27/2016
 * Time: 11:09 PM
 */

namespace BeanGTA\TCore\Tasks;

use pocketmine\scheduler\Task;
use BeanGTA\TCore\Main;
use TCoin\TCoin;

class PopupTask extends Task
{
    public $owner;
    /** @var TCoin */
    public $tcoin;
    public function __construct(Main $owner)
    {
        parent::__construct($owner);
        $this->owner = $owner;
    }

    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return bool
     */

}
