# Frequently Asked Questions

**How do I sign up for an account?**

- Simply by going to our [signup page](/signup) and filling in the form. You can ask for help in \#thunix on tilde.chat, or you can [contact us](contact), if you run into any difficulties.

**How can I request an account recovery or public key replacement?**

- Just send the request from the email you used to register and we'll poke a new key in for you.

**Who is running thunix?**

- The current system administrators are [deepend](/~deepend), [Naglfar](/~naglfar), [ubergeek](/~ubergeek) still helps out from time to time but not sure if he wants the admin role going forward. If this changes it will be updated ASAP.

**What happened to the old thunix? Why the name change?**

- The original machine and founder dissappeared without any warning to anyone, including server staff. For this reason, most things were not backed up, and we needed to obtain a new domain name, and a new set of machines.

**I want a new package installed, or I want something changed on Thunix!**

- Excellent! We're looking to make this system useful for the community! You can submit a PR or an issue [here](https://tildegit.org/thunix/ansible) to request the system change.

**Can I get password-based login? Old thunix had it!**

- No. Sorry. Not for shell access. For other integrated services, password auth will be enabled, but not for your ssh connection. We use key based authentication, as it's more secure, and more convienent for you, to be honest.

**I want to run {fill in the blank} server, but I can't seem to access it?**

- The only exposed ports to the internet are services as defined in our [ansible playbook.](https://tildegit.org/thunix/ansible) If there is a public service you want to see, open an issue, or do a pull request for it, and we'll probably enable it without much question.

**That's too hard! Can you just open the port up for this service I have running?**

- No. Due to security issues, we cannot. HOWEVER! You can certainly use an [SSH tunnel](https://duckduckgo.com/?q=ssh+tunnnel) to access it.

**Old thunix did {fill in the blank}, and now it doesn't. Make it work like it used to!**

- There was a huge changeover. Maybe we can get something going old thunix had, and maybe not. You can mention it in the IRC channel, and we'll see what we can do.

**How can I access my thunix email?**

- You can use the following for your mail settings (This is Thunderbird's setting screen, but the settings are the same):

[![](/media/mail.png)](/media/mail.png)
