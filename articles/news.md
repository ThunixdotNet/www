# State of the Thunix - May 09, 2019

Another month, another update!

Not too much to announce, as far as front end changes. New user requests
should happen much faster now that we've written/stolen a tool from
tilde.team that we can use to expedite user creation, and automation of
a bunch of the steps we take to onboard users.

We're considering adding in user aging for accounts. Lots of accounts
get created, and then never logged into. This can actually post a
security problem for the system, as often times, accounts get created
now, to be used months from today for botnets and the like. Let us know
what your thoughts are on a reasonable time to age off users. At this
time, I am personally leaning towards 180 days. Plenty of time to log
into your shell, and use it a bit. And, once every 6 months isn't much
to ask, since we expect members to actually contribute to the community,
anyways.

We've terminated one account already for running a botnet member from
here, and we'll keep a vigilant eye for any others.

This leads to another point: Just running a znc process doesn't count
for login. Neither does checking your email. You'll need to actually log
into the shell, in order to reset the counter. You should, anyways,
since we have a lot of services internally, that we don't offer external
access too.

We are also looking at terminating the minecraft and minetest instances
here. If anyone is using them, speak up now, or forever hold you peace!
Not really. We wouldn't be deleting anything, just shutting down the
processes, and removing them from the backup scheme.

We do need to welcome our newest sysadmin here: fosslinux. Give them a
warm welcome if you see them around.

And again, any questions, or concerns, feel free to drop myself, or any
of the other admins a line.

Ubergeek/ub3g33k
