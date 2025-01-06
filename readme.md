# WordPress GitHub Repository Card

A WordPress plugin that allows you to embed GitHub repository cards in your posts and pages using the block editor (Gutenberg).


## Features

- 🎯 Easy to use block editor integration
- 📊 Display repository information:
  - Repository name and description
  - Star count and fork count
  - Primary language
  - Author avatar
- 💻 Modern visual effects:
  - Animated gradient border
  - Breathing light effect
  - Smooth hover transitions
- 💻 Clean and modern design
- 🌓 Dark mode support
- 📱 Fully responsive
- 🔑 GitHub API authentication support
  - Increase API rate limit from 60 to 5000 requests per hour
  - Secure token storage

## Installation

1. Download the plugin
2. Upload to your WordPress site's `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

## Usage

1. In the block editor, click the '+' button to add a new block
2. Search for "GitHub" or "Repository"
3. Select the "GitHub Repository Card" block
4. Enter the GitHub username and repository name
5. The card will be automatically rendered in your post/page

## Configuration

### Setting up GitHub Authentication (Recommended)

1. Go to [GitHub Personal Access Tokens](https://github.com/settings/tokens)
2. Click "Generate new token"
3. Give your token a name (e.g., "WordPress Repo Card")
4. Select only the `public_repo` permission
5. Copy the generated token
6. In WordPress admin, go to Settings > GitHub Repository Card
7. Paste your token and save changes

This will increase your API rate limit from 60 to 5000 requests per hour.

## Development

### Prerequisites

- Node.js and npm
- WordPress development environment

### Setup

#### Clone the repository

```bash
git clone https://github.com/sucooer/wp-github-repo-card.git
```

#### Navigate to the plugin directory

```bash
cd wp-github-repo-card
```

#### Install dependencies

```bash
npm install
```

#### Build the block

```bash
npm run build
```

## License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.
