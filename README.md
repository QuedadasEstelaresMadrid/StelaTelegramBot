# Stela Telegram Bot

## What is?

info [official site](https://quedadasestelaresmadrid.com).

## Installation instructions
Tested on LAMP servers over SSL.


### Codeigniter install
Unzip or clone the latest version of Stela into web path (usually /var/www/html). Stela is a CodeIgniter4 implementation.

Set configuration files with the corresponding data:
> app/config/app.php
> application/config/database.php

### MySQL install
```
CREATE TABLE `stela_summary` (
  `id` int(11) NOT NULL,
  `msg_from` varchar(1024) NOT NULL,
  `msg` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `msg_id` int(11) NOT NULL,
  `msg_group` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `stela_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `stela_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;
```
### Stela code config

under construction

### Telegram config

under construction





