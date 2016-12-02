<?php
$PluginInfo['promoteOnPostCount'] = [
    'Name' => 'Promote on Post Count',
    'Description' => 'Allows role promotion based on post count',
    'Version' => 'alpha',
    'RequiredApplications' => ['Vanilla' => '2.3'],
    'MobileFriendly' => true,
    'HasLocale' => true,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'https://vanillaforums.org/profile/R_J',
    'SettingsUrl' => '/settings/promoteonpostcount',
    'License' => 'MIT'
];
class PromoteOnPostCountPlugin extends Gdn_Plugin {
    /**
     * Prefill settings with sane settings.
     *
     * @return void.
     */
    public function setup() {
        touchConfig(
            'promoteOnPostCount.FromRoleID',
            RoleModel::getDefaultRoles(RoleModel::TYPE_APPLICANT)
        );
        touchConfig(
            'promoteOnPostCount.ToRoleID',
            RoleModel::getDefaultRoles(RoleModel::TYPE_MEMBER)
        );
    }

    public function settingsController_promoteOnPostCount_create($sender) {
        $sender->permission('Garden.Settings.Manage');

        $sender->addSideMenu('dashboard/settings/plugins');

        $sender->setData('Title', t('Promotion Rule'));

        // Get role names.
        $roleModel = new RoleModel();
        $roles = $roleModel->getArray();
        // Filter out admins and mods (too dangerous).
        $adminRoles = $roleModel->getbyType($roleModel::TYPE_ADMINISTRATOR);
        foreach ($adminRoles as $role) {
            unset($roles[$role->RoleID]);
        }
        $modRoles = $roleModel->getbyType($roleModel::TYPE_MODERATOR);
        foreach ($modRoles as $role) {
            unset($roles[$role->RoleID]);
        }

        $sender->setData('AvailableRoles', $roles);

        $validation = new Gdn_Validation();
        $configurationModel = new Gdn_ConfigurationModel($validation);
        $configurationModel->setField(
            [
                'promoteOnPostCount.MinComments',
                'promoteOnPostCount.Connector',
                'promoteOnPostCount.MinDiscussions',
                'promoteOnPostCount.FromRoleID',
                'promoteOnPostCount.ToRoleID'
            ]
        );
        $sender->Form->setModel($configurationModel);

        if ($sender->Form->authenticatedPostBack() === false) {
            // If form is displayed "unposted".
            $sender->Form->setData($configurationModel->Data);
        } else {
            // Validate posted form.
            $sender->Form->validateRule('promoteOnPostCount.MinComments', 'ValidateRequired');
            $sender->Form->validateRule('promoteOnPostCount.MinComments', 'ValidateInteger');
            $sender->Form->validateRule('promoteOnPostCount.Connector', 'ValidateRequired');
            $connector = (object)[];
            $connector->Enum = ['AND', 'OR'];
            $sender->Form->validateRule(
                'promoteOnPostCount.Connector',
                ['Name' => 'ValidateEnum', 'Args' => $connector]
            );
            $sender->Form->validateRule('promoteOnPostCount.MinDiscussions', 'ValidateRequired');
            $sender->Form->validateRule('promoteOnPostCount.MinDiscussions', 'ValidateInteger');
            $sender->Form->validateRule('promoteOnPostCount.FromRoleID', 'ValidateRequired');
            $sender->Form->validateRule('promoteOnPostCount.FromRoleID', 'ValidateInteger');
            $sender->Form->validateRule('promoteOnPostCount.ToRoleID', 'ValidateRequired');
            $sender->Form->validateRule('promoteOnPostCount.ToRoleID', 'ValidateInteger');

            // Try saving values.
            if ($sender->Form->save() !== false) {
                $sender->informMessage(
                    sprite('Check', 'InformSprite').t('Your settings have been saved.'),
                    ['CssClass' => 'Dismissable AutoDismiss HasSprite']
                );
            }
        }
        $sender->render($this->getView('settings.php'));
    }

    public function vanillaController_promote_create($sender) {
        $args = [];
        $args['CommentData'] = [];
        $args['CommentData']['InsertUserID'] = '5';

        $this->commentModel_afterSaveComment_handler($sender, $args);
    }

    /**
     * Change role if several conditions are met.
     *
     * Insert user must have minimum comments and discussions, must be in a
     * specific role and not already part of the target role.
     *
     * @param CommentModel $sender Instance of the calling class.
     * @param Mixed        $args   Event arguments.
     *
     * @return void.
     */
    public function commentModel_afterSaveComment_handler($sender, $args) {
        // Exit if comments shouldn't be checked.
        $minComments = c('promoteOnPostCount.MinComments', 0);
        if ($minComments == 0) {
            return;
        }
        // Get insert user.
        $user = Gdn::userModel()->getID($args['CommentData']['InsertUserID']);
        if (!$user) {
            return;
        }
        // Exit if user hasn't the needed number of comments or discussions.
        $minDiscussions = c('promoteOnPostCount.MinDiscussions', 0);
        if (
            $user->CountComments < $minComments ||
            ($minDiscussions > 0 && $user->CountDiscussions < $minDiscussions)
        ) {
            return;
        }
        // Exit if user hasn't FromRoleID or already has ToRoleID.
        $userRoles = array_column(
            Gdn::userModel()->getRoles($user->UserID)->resultArray(),
            'RoleID'
        );
        if (
            !in_array(c('promoteOnPostCount.FromRoleID', true), $userRoles) ||
            in_array(c('promoteOnPostCount.ToRoleID', true), $userRoles)
        ) {
             return;
        }
        // Everything is fine. Assign new role.
        Gdn::userModel()->saveRoles($user->UserID, c('promoteOnPostCount.ToRoleID'), true);
        $this->roleChangeActivity($user);
    }

    /**
     * Change role if several conditions are met.
     *
     * Insert user must have minimum comments and discussions, must be in a
     * specific role and not already part of the target role.
     *
     * @param DiscussionModel $sender Instance of the calling class.
     * @param Mixed           $args   Event arguments.
     *
     * @return void.
     */
    public function discussionModel_afterSaveComment_handler($sender, $args) {
        // Exit if comments shouldn't be checked.
        $minDiscussions = c('promoteOnPostCount.MinDiscussions', 0);
        if ($minDiscussions == 0) {
            return;
        }
        // Get insert user.
        $user = Gdn::userModel()->getID($args['DiscussionData']['InsertUserID']);
        if (!$user) {
            return;
        }
        // Exit if user hasn't the needed number of comments or discussions.
        $minComments = c('promoteOnPostCount.MinComments', 0);
        if (
            $user->CountDiscussions < $minDiscussions ||
            ($minComments > 0 && $user->CountComments < $minComments)
        ) {
            return;
        }
        // Exit if user hasn't FromRoleID or already has ToRoleID.
        $userRoles = array_column(
            Gdn::userModel()->getRoles($user->UserID)->resultArray(),
            'RoleID'
        );
        if (
            !in_array(c('promoteOnPostCount.FromRoleID', true), $userRoles) ||
            in_array(c('promoteOnPostCount.ToRoleID', true), $userRoles)
        ) {
             return;
        }
        // Everything is fine. Assign new role.
        Gdn::userModel()->saveRoles($user->UserID, c('promoteOnPostCount.ToRoleID'), true);
        $this->roleChangeActivity($user);
    }

    private function roleChangeActivity($user) {
        $activityModel = new ActivityModel();
        $HeadlineFormat = t('HeadlineFormat.RoleChange', '{ActivityUserID,your} gained more permissions!');
//        $HeadlineFormat = t('HeadlineFormat.PictureChange.ForUser', '{RegardingUserID,You} changed the profile picture for {ActivityUserID,user}.');

        $activityModel->save([
            'ActivityUserID' => $user->UserID,
            // 'RegardingUserID' => Gdn::session()->UserID,
            'ActivityType' => 'RoleChange',
            'HeadlineFormat' => $HeadlineFormat
            // 'Story' => img($PhotoUrl, array('alt' => t('Thumbnail')))
        ]);
    }
}
