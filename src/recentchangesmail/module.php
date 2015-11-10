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
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;

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

    /**
     * Returns the last changes for 1 day.
     *
     * @return \string[]
     */
    public function getRecentChanges() {
        // TODO: Make configurable.
        $days = 1;
        $found_facts = FunctionsDb::getRecentChanges(WT_CLIENT_JD - $days);

        return $found_facts;
    }

    /**
     * Returns the changeset as plain text.
     *
     * @param $found_facts
     * @return string
     */
    public function getRecentChangesAsHtml() {
        // TODO: Refactor this code taken from RecentChangesModule.
        global $WT_TREE;
        $sort = "date_desc";

        $found_facts = $this->getRecentChanges();

        $n   = 0;
        $arr = array();
        foreach ($found_facts as $change_id) {
            $record = GedcomRecord::getInstance($change_id, $WT_TREE);
            if (!$record || !$record->canShow()) {
                continue;
            }
            // setup sorting parameters
            $arr[$n]['record'] = $record;
            $arr[$n]['jd']     = ($sort == 'name') ? 1 : $n;
            $arr[$n]['anniv']  = $record->lastChangeTimestamp(true);
            $arr[$n++]['fact'] = $record->getSortName(); // in case two changes have same timestamp
        }

        uasort($arr, '\Fisharebest\Webtrees\Functions\Functions::eventSort');
        $arr = array_reverse($arr);

        $mailtext = '';
        foreach ($arr as $value) {
            $mailtext .= '<a href="' . $value['record']->getHtmlUrl() . '" class="list_item name2">' . $value['record']->getFullName() . '</a>';
            $mailtext .= '<div class="indent" style="margin-bottom: 5px;">';
            if ($value['record'] instanceof Individual) {
                if ($value['record']->getAddName()) {
                    $mailtext .= '<a href="' . $value['record']->getHtmlUrl() . '" class="list_item">' . $value['record']->getAddName() . '</a>';
                }
            }
            $mailtext .= /* I18N: [a record was] Changed on <date/time> by <user> */
                I18N::translate('Changed on %1$s by %2$s', $value['record']->lastChangeTimestamp(), Filter::escapeHtml($value['record']->lastChangeUser()));
            $mailtext .= '</div>';
        }

        return $mailtext;
    }

    public function getMailText() {
        // TODO: Translate
        $mailtext = "The following changes were made:\n\n";
        $mailtext .= $this->getRecentChangesAsHtml();

        // TODO: add link

        return $mailtext;
    }

}

return new RecentChangesMail();