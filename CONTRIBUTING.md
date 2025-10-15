# Contributing to Marcello Scavo Tattoo Theme

Thank you for your interest in contributing to the Marcello Scavo Tattoo Theme! This document provides guidelines and information for contributors.

## 🔧 Development Setup

### Prerequisites

- **PHP**: 7.4 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 16 or higher (for asset compilation)
- **WordPress**: 6.0 or higher (for testing)

### Local Development Environment

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-username/marcello-scavo-tattoo.git
   cd marcello-scavo-tattoo
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install --dev
   ```

3. **Set up WordPress environment**:
   - Install the theme in a local WordPress installation
   - Activate the theme in WordPress admin

## 📋 Code Standards

### WordPress Coding Standards

This project follows the [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/). All code must pass PHPCS analysis.

#### Running Code Quality Checks

```bash
# Check all files
./vendor/bin/phpcs --standard=phpcs.xml

# Check specific file
./vendor/bin/phpcs --standard=phpcs.xml path/to/file.php

# Auto-fix issues (where possible)
./vendor/bin/phpcbf --standard=phpcs.xml

# Using the dev tools script
./dev-tools.sh lint
./dev-tools.sh fix
```

### Code Quality Requirements

- ✅ **100% PHPCS Compliance**: No errors or warnings
- ✅ **Security**: All inputs sanitized, outputs escaped
- ✅ **Performance**: Optimized for speed and memory usage
- ✅ **Documentation**: PHPDoc blocks for all functions
- ✅ **Accessibility**: WCAG 2.1 AA compliance
- ✅ **Internationalization**: Proper text domains and translation functions

## 🏗️ Architecture Guidelines

### File Organization

```
marcello-scavo-tattoo/
├── inc/                    # PHP modules (modular architecture)
│   ├── setup.php          # Theme initialization
│   ├── enqueue.php         # Asset management
│   ├── performance-*.php   # Performance optimization
│   └── ...                 # Other specialized modules
├── assets/                 # Frontend assets
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript
│   └── images/            # Images
├── languages/             # Translation files
└── vendor/                # Composer dependencies
```

### Naming Conventions

- **Functions**: `marcello_scavo_function_name()`
- **Variables**: `$variable_name`
- **Constants**: `MARCELLO_SCAVO_CONSTANT_NAME`
- **Classes**: `Marcello_Scavo_Class_Name`
- **Files**: `kebab-case.php`

### Security Guidelines

```php
// Input Sanitization
$user_input = sanitize_text_field(wp_unslash($_POST['field']));

// Output Escaping
echo esc_html($content);
echo esc_url($url);
echo esc_attr($attribute);

// Nonce Verification
wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'action');

// Capability Checks
if (!current_user_can('manage_options')) {
    wp_die(__('Access denied', 'marcello-scavo-tattoo'));
}
```

## 🧪 Testing

### Automated Testing

```bash
# Run all tests
./dev-tools.sh test

# PHP Compatibility Testing
./vendor/bin/phpcs --standard=PHPCompatibilityWP --runtime-set testVersion 7.4-

# WordPress Standards
./vendor/bin/phpcs --standard=WordPress

# Performance Testing
./dev-tools.sh performance
```

### Manual Testing Checklist

- [ ] Theme activates without errors
- [ ] All pages load correctly
- [ ] Customizer options work properly
- [ ] Performance optimizations are active
- [ ] Multi-language functionality works
- [ ] Mobile responsiveness is maintained
- [ ] Accessibility requirements are met

## 📝 Commit Guidelines

### Commit Message Format

```
type(scope): subject

body

footer
```

### Types

- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code style changes (formatting, etc.)
- **refactor**: Code refactoring
- **perf**: Performance improvements
- **test**: Adding or updating tests
- **chore**: Maintenance tasks

### Examples

```bash
feat(performance): add critical CSS inlining

Implement automatic critical CSS extraction and inlining
for above-the-fold content to improve page load times.

Closes #123
```

```bash
fix(security): sanitize contact form inputs

Add proper sanitization for all contact form fields
to prevent XSS vulnerabilities.

Fixes #456
```

## 🔄 Pull Request Process

### 1. Fork and Branch

```bash
# Fork the repository on GitHub
git clone https://github.com/your-username/marcello-scavo-tattoo.git
cd marcello-scavo-tattoo

# Create a feature branch
git checkout -b feature/amazing-new-feature
```

### 2. Make Changes

- Follow the code standards outlined above
- Write clear, concise commit messages
- Include tests for new functionality
- Update documentation as needed

### 3. Test Your Changes

```bash
# Run code quality checks
./dev-tools.sh lint

# Test functionality
./dev-tools.sh test

# Performance testing
./dev-tools.sh performance
```

### 4. Submit Pull Request

1. **Push your branch**:
   ```bash
   git push origin feature/amazing-new-feature
   ```

2. **Create Pull Request** on GitHub with:
   - Clear title and description
   - Reference to related issues
   - Screenshots for UI changes
   - Testing instructions

### 5. Code Review Process

- Maintainers will review your PR
- Address any feedback or requested changes
- Once approved, your PR will be merged

## 📊 Performance Considerations

### Performance Requirements

- **Page Load Time**: < 3 seconds
- **Time to First Byte**: < 800ms
- **First Contentful Paint**: < 1.5 seconds
- **Largest Contentful Paint**: < 2.5 seconds
- **Cumulative Layout Shift**: < 0.1

### Optimization Guidelines

```php
// Conditional Asset Loading
if (is_singular('portfolio')) {
    wp_enqueue_style('portfolio-styles', /* ... */);
}

// Database Query Optimization
$posts = get_posts([
    'post_type' => 'portfolio',
    'numberposts' => 10,
    'meta_query' => $meta_query,
    'no_found_rows' => true, // Skip pagination queries
    'update_post_term_cache' => false, // Skip term cache if not needed
]);

// Image Optimization
add_image_size('portfolio-thumbnail', 400, 300, true);
```

## 🌍 Internationalization

### Translation Guidelines

```php
// Text Strings
__('Text to translate', 'marcello-scavo-tattoo');
_e('Text to echo', 'marcello-scavo-tattoo');

// Pluralization
_n('One item', '%d items', $count, 'marcello-scavo-tattoo');

// Context
_x('Post', 'noun', 'marcello-scavo-tattoo');

// Escaping with Translation
esc_html__('Safe text', 'marcello-scavo-tattoo');
```

### Translation Files

- **POT File**: Generated automatically from source code
- **PO Files**: Translation files for each language
- **MO Files**: Compiled binary translation files

## 🐛 Bug Reports

### Before Submitting

1. Check existing issues for duplicates
2. Test with default WordPress theme
3. Disable all plugins
4. Check browser console for errors

### Bug Report Template

```markdown
**Bug Description**
A clear description of the bug.

**Steps to Reproduce**
1. Go to '...'
2. Click on '...'
3. See error

**Expected Behavior**
What you expected to happen.

**Screenshots**
Add screenshots if applicable.

**Environment**
- WordPress Version: 
- PHP Version: 
- Browser: 
- Theme Version: 
```

## 💡 Feature Requests

### Feature Request Template

```markdown
**Feature Description**
Clear description of the feature.

**Use Case**
Why would this feature be useful?

**Implementation Ideas**
Any ideas on how to implement this.

**Additional Context**
Any other context or screenshots.
```

## 📄 License

By contributing to this project, you agree that your contributions will be licensed under the same [GPL v2.0 or later](https://www.gnu.org/licenses/gpl-2.0.html) license that covers the project.

## 🆘 Getting Help

- **Documentation**: Check the README.md and inline documentation
- **Issues**: Search existing GitHub issues
- **Discussions**: Use GitHub Discussions for questions
- **Discord**: Join our development Discord server
- **Email**: Contact the maintainers at dev@marcelloscavo.com

## 🏆 Recognition

Contributors are recognized in:
- README.md acknowledgments
- GitHub contributors page
- Release notes for significant contributions
- Annual contributor appreciation posts

---

**Thank you for contributing! 🎉**

Every contribution, no matter how small, helps make this theme better for the WordPress community.