# State Of The Thunix - February 2020

New month Thunixers, and latest update!

Things are moving along swimmingly, more or less.  We implemented a new
account recovery feature, which users are taking advantage of.  And,
if you're in the IRC channel, you've been noticing a new project being
worked on:  The Thunix API.  Also, we were planning a migration to new
hardware this month, but that has been postponed.

If you've not set up your account recovery information, please make sure
you do so, soon, to ensure you can recover your account in the future,
should the need arise.  Just put an email address or recovery passphrase
in ~/.thunix/recovery, and chmod 600 that file.  This ensures only you
can see it (And the admin team when we need to), and we have a way of
reaching out and verifying it is you requesting the recovery.

We are projecting to do the hardware migration in February, but it is
still in flux a little.  We'll let you know in IRC, and via email when
the time comes.  You shouldn't notice anything different other than a
new IP address, but we will preserve the ssh keys, so you won't have to
worry about ssh breaking.

Also, we are working on an API to get info about the Thunix
systems, and also, to do some of the management for it, remotely.
This will enable us to (in the future) build some pretty awesome
apps around the system itself.  If you would like to help with
the API development work, feel free to join us at [thunix_api git
repo](https://tildegit.org/thunix/thunix_api) :)
