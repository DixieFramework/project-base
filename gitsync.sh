#!/usr/bin/env bash

action=$1

if [ "$action" == 'init' ]
    then
        rm -rf lib/Dixie/Bundle/*
        rm -rf lib/Dixie/Component/*
        git commit -a -m "Commiting changes before initializing subtrees"

        git subtree add --prefix=lib/Dixie/Component/User git@github.com:DixieFramework/User.git main
        git subtree add --prefix=lib/Dixie/Component/Resource git@github.com:DixieFramework/Resource.git main
        git subtree add --prefix=lib/Dixie/Component/Registry git@github.com:DixieFramework/Registry.git main
        git subtree add --prefix=lib/Dixie/Component/Media git@github.com:DixieFramework/Media.git main
        git subtree add --prefix=lib/Dixie/Component/Subscription git@github.com:DixieFramework/Subscription.git main

        git subtree add --prefix=lib/Dixie/Bundle/UserBundle git@github.com:DixieFramework/UserBundle.git main
        git subtree add --prefix=lib/Dixie/Bundle/ResourceBundle git@github.com:DixieFramework/ResourceBundle.git main
        git subtree add --prefix=lib/Dixie/Bundle/MediaBundle git@github.com:DixieFramework/MediaBundle.git main
        git subtree add --prefix=lib/Dixie/Bundle/AvatarBundle git@github.com:DixieFramework/AvatarBundle.git main
        git subtree add --prefix=lib/Dixie/Bundle/StripeBundle git@github.com:DixieFramework/StripeBundle.git main
fi

if [ "$action" == 'push' ]
    then
        git commit -a -m "Pushing changes before synchronizing subtrees"

        git subtree push --prefix=lib/Dixie/Component/User git@github.com:DixieFramework/User.git main
        git subtree push --prefix=lib/Dixie/Component/Resource git@github.com:DixieFramework/Resource.git main
        git subtree push --prefix=lib/Dixie/Component/Registry git@github.com:DixieFramework/Registry.git main
        git subtree push --prefix=lib/Dixie/Component/Media git@github.com:DixieFramework/Media.git main
        git subtree push --prefix=lib/Dixie/Component/Subscription git@github.com:DixieFramework/Subscription.git main

        git subtree push --prefix=lib/Dixie/Bundle/UserBundle git@github.com:DixieFramework/UserBundle.git main
        git subtree push --prefix=lib/Dixie/Bundle/ResourceBundle git@github.com:DixieFramework/ResourceBundle.git main
        git subtree push --prefix=lib/Dixie/Bundle/MediaBundle git@github.com:DixieFramework/MediaBundle.git main
        git subtree push --prefix=lib/Dixie/Bundle/AvatarBundle git@github.com:DixieFramework/AvatarBundle.git main
        git subtree push --prefix=lib/Dixie/Bundle/StripeBundle git@github.com:DixieFramework/StripeBundle.git main
fi

if [ "$action" == 'pull' ]
    then
        git commit -a -m "Pushing changes before pulling subtrees"

        git subtree pull --prefix=lib/Dixie/Component/User git@github.com:DixieFramework/User.git main
        git subtree pull --prefix=lib/Dixie/Component/Resource git@github.com:DixieFramework/Resource.git main
        git subtree pull --prefix=lib/Dixie/Component/Registry git@github.com:DixieFramework/Registry.git main
        git subtree pull --prefix=lib/Dixie/Component/Media git@github.com:DixieFramework/Media.git main
        git subtree pull --prefix=lib/Dixie/Component/Subscription git@github.com:DixieFramework/Subscription.git main

        git subtree pull --prefix=lib/Dixie/Bundle/UserBundle git@github.com:DixieFramework/UserBundle.git main
        git subtree pull --prefix=lib/Dixie/Bundle/ResourceBundle git@github.com:DixieFramework/ResourceBundle.git main
        git subtree pull --prefix=lib/Dixie/Bundle/MediaBundle git@github.com:DixieFramework/MediaBundle.git main
        git subtree pull --prefix=lib/Dixie/Bundle/AvatarBundle git@github.com:DixieFramework/AvatarBundle.git main
        git subtree pull --prefix=lib/Dixie/Bundle/StripeBundle git@github.com:DixieFramework/StripeBundle.git main
fi
