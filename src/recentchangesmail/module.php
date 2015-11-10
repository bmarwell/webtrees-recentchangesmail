<?php
/**
 * webtrees recentchangesmail: online genealogy recent-changes-mail-module.
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace bmarwell\WebtreesModules\RecentChangesMail;

use Fisharebest\webtrees\Auth;

use Composer\Autoload\ClassLoader;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;

/**
 * Class RecentChangesMail
 * @package bmarwell\WebtreesModules\RecentChangesMail
 */
class RecentChangesMail extends AbstractModule implements ModuleConfigInterface {
    /*
     * ***************************
     * Module configuration
     * ***************************
     */
    /** @var string location of the fancy treeview module files */
    var $directory;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('RecentChangesMail');
        $this->directory = WT_MODULES_DIR . $this->getName();
        $this->action = Filter::get('mod_action');
        // register the namespaces
        $loader = new ClassLoader();
        $loader->addPsr4('bmarwell\\WebtreesModules\\RecentChangesMail\\', $this->directory);
        $loader->register();
    }

    /**
     * @return string
     */
    public function getName() {
        return "recentchangesmail";
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle() {
        return "Recent Changes Mail";
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription() {
        return "Sends a mail every evening about changes.";
    }

    /**
     * {@inheritdoc}
     */
    public function defaultAccessLevel() {
        return Auth::PRIV_PRIVATE;
    }

    /**
     * {@inheritdoc}
     */
    function modAction($modAction) {
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigLink() {
        return 'module.php?mod=' . $this->getName () . '&amp;mod_action=admin_config';
    }

}

return new RecentChangesMail();