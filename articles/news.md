# State of the Thunix - November 2019

Yep, Turkey Month (For US thunixers)!

Holidays are coming up, and Thunix is approaching our 1 year anniversary!  I hope you've enjoyed your stay thus far, and looking foward to many more years!  Someday, we'll be older than SDF :P

We're still working towards migration of the hardware, onto something that is a little bigger, hopefully a little cheaper or the same cost, so we have more room to grow.  Mainly, our constraint is IP addresses, which isn't too big of a deal.  The main reason for it is so we can get a proper hypervisor in place, which will afford us some more flexibility in what we can offer as services.

Over the past month, we did get a couple of abuse notices, for people running malware.  As a reminder:  Don't do that here.  Most of the time, I notice things awry, and will shut it down, or, we get an abuse notice, and I go on to do the same, but more in depth.  Your account will be terminated immediately, and your files will be handed over to the organization reporting it upon demand, and you will not get a second chance.

We also got a DMCA takedown request, for people trying to host pirated content here.  I work on a best-faith idea here, you end up with 1 warning, and no more.  Afterwards, your account will be terminated.  This isn't a place to host your movies, your warez, or anything of that sort.

We have to come down hard on folks abusing the resources here, to ensure the community continues for everyone.  It's pretty easy for one rotten apple to ruin it all.  Don't be the rotten apple.

Another thing to keep in mind:  While we will make all attempts to back up your data you have here, there are some caveats:
* We only keep 3 days of backups.
* Excessively large home directories will not be backed up
* Configuration files are not backed up, but kept in source control

We only keep 3 days worth due to privacy concerns, mostly.  We don't want to keep your personally identifiable information any longer than is needed.  We also limit it due to space concerns.  Which brings us to the large home directories.

If your home dir is bigger than most others, it's likely not being backed up.  For example, I've excluded my home dir from backups, because my generally is over 1GB in size,  There are some that are multiples of that, which is fine, to a point.   But they are not going to be backed up, due to space and causing the backup jobs to run excessively long.  If you want your home dir backed up, but it's large, you can let us know which sub directories you may have that can be excluded (ie, git repo clones, as an example).  Just ask in IRC, or by email to root.

Well, that sums it up.  I hope everyone is enjoying the use of the system here, and with my usual schpliel:  If you want changes made, open an issue, or a PR in our repos!  Happy holidays, and we'll update here again after the US Turkey Day :)


Your friendly neighborhood sysadmin,
ubergeek/ub3g33k
