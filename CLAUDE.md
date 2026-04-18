# Plantaphilia - WordPress OER Project

## Project Overview
This is a WordPress-based Open Educational Resources (OER) project for plant education and biology.

## Code Style
- PHP: Follow WordPress coding standards
- Use semantic HTML5
- Maintain consistent indentation (4 spaces for PHP)

## Project Structure
- `app/public/` - WordPress installation
  - `wp-content/themes/Impreza-child/` - Custom child theme
  - `wp-content/plugins/` - Custom plugins
- `conf/` - Server configuration files (nginx, php, mysql)
- `logs/` - Application logs (not in git)

## Key Files for Claude
- `app/public/wp-content/themes/Impreza-child/functions.php` - Theme functions and hooks
- `app/public/wp-content/themes/Impreza-child/page-produkt-liste.php` - Product listing template
- `app/public/wp-config.php` - WordPress configuration (not in git - use local copy)

## Development Workflow
1. Make changes to theme files in `wp-content/themes/Impreza-child/`
2. Test changes in local environment
3. Commit changes with descriptive messages
4. Push to GitHub for Claude Code review and collaboration

## Important Notes
- Database dumps are excluded from git (too large for Claude context)
- Media uploads are excluded (use separate media storage)
- Server configs are for local development only
- Always check `wp-config.php` locally for database credentials
