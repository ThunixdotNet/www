# Frequently Asked Questions

**How do I sign up for an account?**

- Go to the [signup page](/signup) and fill out the form.
- If you get stuck, ask in **#thunix** on **newnet.net**, or [contact us](/contact).

**How can I request an account recovery or public key replacement?**

- Email us **from the address you used to register** and tell us:
  - your username
  - what you need (recovery / key replacement)
  - your **new public key** (paste it in the email)
- We’ll swap the key and let you know when it’s done.

**Who is running thunix?**

- Current system administrators: [deepend](/~deepend), [Naglfar](/~naglfar)

**What happened to the old thunix? Why the name change?**

- The original machine and founder disappeared without warning (including to staff).
- Most things weren’t backed up, so we rebuilt on new machines and moved to a new domain.

**I want a new package installed, or I want something changed on Thunix!**

- Good. That’s how systems become useful instead of decorative.
- Ask in **#thunix** on **newnet.net**, or [contact us](/contact) with:
  - what you want
  - why you want it
  - whether it needs to be available to everyone or just you

**Can I get password-based login? Old thunix had it!**

- No. Not for **shell access**.
- SSH is **key-based** because it’s more secure and, honestly, less annoying once you’re set up.
- Other services (like email) use passwords because that’s how the world works.

**That’s too hard! Can you just open the port up for this service I have running?**

- No.
- If you need access to something you’re running, use an **SSH tunnel**.
  - Example (adjust ports as needed):
    - `ssh -L 8080:127.0.0.1:8080 youruser@YOUR_SSH_HOSTNAME`

**Old thunix did {fill in the blank}, and now it doesn't. Make it work like it used to!**

- There was a big changeover. Some old stuff can come back, some can’t.
- Mention it in **#thunix** and we’ll see what’s realistic.

**How can I access my thunix email?**

- Use these settings in Thunderbird, Apple Mail, Outlook, mutt, a ham radio, whatever.

## Incoming Mail (IMAP)
- **Server:** `thunix.net`
- **Username:** `yourusername`
- **Password:** your **mail/service** password (not your SSH key)
- **Security:** SSL/TLS
- **Port:** `993`

## Outgoing Mail (SMTP)
- **Server:** `thunix.net`
- **Authentication:** Yes (same username/password as above)
- **Security:** STARTTLS
- **Port:** `587`
