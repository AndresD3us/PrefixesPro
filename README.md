---

<p align="center">
  <img src="https://img.shields.io/badge/plugin-PrefixesPro-blueviolet?style=for-the-badge">
  <br><br>
</p>

<h1 align="center">✨ PrefixesPro</h1>
<p align="center">Advanced prefix system for PocketMine-MP. Create, assign, and manage prefixes with categories, expiration times, GUI menus, and much more!</p>

---

## 💡 Features

- ✅ Prefixes with color & formatting support (`&` color codes)
- 🏷️ Two prefix categories: **Free** (anyone can activate) and **Pay** (admin-assigned)
- ⏳ Expiration system — assign prefixes with a duration (`30d`, `1h`, `30m`, etc.)
- 🛡️ Permission-based access control
- 🧠 Per-player session management (active prefix, owned prefixes)
- 📦 Full GUI interface via FormAPI for prefix selection and creation
- 🔁 Auto-save system with configurable interval
- 🏷️ Nametag update support (optional)
- 📝 Fully customizable chat format with `{prefix}`, `{name}`, and `{message}` placeholders
- ⚡ Lightweight and optimized for PocketMine-MP API 5.x

---

## 🛠️ Installation

1. 📥 Download the `.phar` file
2. 🔌 Drag it into your server's `/plugins/` folder
3. 🚀 Restart your server
4. ✅ Use `/prefixes` in-game to open the GUI menu

---

## 📚 Commands

| Command | Description | Permission |
|---|---|---|
| `/prefixes` | Opens the prefix GUI menu | `prefixespro.gui` |
| `/prefix help` | Shows the list of available commands | `prefixespro.command.help` |
| `/prefix create` | Opens a form to create a new prefix | `prefixespro.command.create` |
| `/prefix delete <name>` | Deletes a prefix from the server | `prefixespro.command.delete` |
| `/prefix add <player> <prefix> [time]` | Assigns a prefix to a player (with optional duration) | `prefixespro.command.add` |
| `/prefix remove <player> <prefix>` | Removes a prefix from a player | `prefixespro.command.remove` |
| `/prefix list` | Lists all registered prefixes | `prefixespro.command.list` |
| `/prefix reload` | Reloads config, prefixes, sessions, and messages | `prefixespro.command.reload` |

> 💡 Most subcommands have aliases: `add` → `give`, `assign` | `remove` → `take` | `create` → `new` | `list` → `all`

---

## ⏳ Time Format

When assigning prefixes with `/prefix add`, you can specify an optional duration:

| Format | Meaning |
|---|---|
| `30s` | 30 seconds |
| `10m` | 10 minutes |
| `2h` | 2 hours |
| `7d` | 7 days |
| `1w` | 1 week |
| `1d12h` | Combined durations |
| *(no argument)* | Permanent |

**Example:** `/prefix add Steve VIP 30d` — assigns the prefix `VIP` to Steve for 30 days.

---

## 🏷️ Prefix Categories

Prefixes have two categories that control how players can obtain them:

- **Free** — any player with the right permission node can activate it from the GUI (`/prefixes`).
- **Pay** — must be manually assigned by an admin using `/prefix add`. Ideal for donor or purchased ranks.

You can set the category when creating a prefix via the in-game form (`/prefix create`).

---

## 🔑 Permissions

| Permission | Default | Description |
|---|---|---|
| `prefixespro.command` | `true` | Allows using `/prefix` |
| `prefixespro.command.help` | `true` | View command help |
| `prefixespro.gui` | `true` | Open the prefix GUI menu |
| `prefixespro.command.create` | `op` | Create a new prefix |
| `prefixespro.command.delete` | `op` | Delete a prefix |
| `prefixespro.command.add` | `op` | Assign a prefix to a player |
| `prefixespro.command.remove` | `op` | Remove a prefix from a player |
| `prefixespro.command.list` | `op` | View all registered prefixes |
| `prefixespro.command.reload` | `op` | Reload the plugin |

---

## ⚙️ Configuration

```yaml
# config.yml

# Maximum length of the prefix name (without colors)
prefix-max-name-length: 10

# Maximum length of the prefix format (with color codes)
prefix-max-format-length: 30

# Time (in seconds) between each auto-save
autosave-interval: 300

# Chat format — placeholders: {prefix} {name} {message}
chat-format: "{prefix} §7{name}§r: {message}"

# Apply the active prefix to the player's nametag
update-nametag: false
```

---

## 📁 File Structure

```
plugin_data/
└── PrefixesPro/
    ├── prefixes/
    │   └── <prefix>.yml
    ├── sessions/
    │   └── <uuid>.yml
    ├── messages.yml
    └── config.yml
```

---

## 📖 License

This plugin is open source and licensed under the [MIT License](LICENSE).
Feel free to fork, contribute, or open issues and pull requests.

---

<p align="center"><b>Made with ❤️ by AndresD3us</b></p>

---
