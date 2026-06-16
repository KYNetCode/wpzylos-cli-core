# WPZylos CLI Core

[![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![GitHub](https://img.shields.io/badge/GitHub-KYNetCode-181717?logo=github)](https://github.com/KYNetCode/wpzylos-cli-core)

Stub compilation and file generation utilities for building CLI tools and code generators.

📖 **[Full Documentation](https://wpzylos.com)** | 🐛 **[Report Issues](https://github.com/KYNetCode/wpzylos-cli-core/issues)**

---

## ✨ Features

- **StubCompiler** — Replace placeholders in stub templates with dynamic values
- **FileWriter** — Write files safely with automatic directory creation
- **Generator Base** — Abstract base class for building custom generators
- **Context-Aware Compilation** — Built-in support for plugin context tokens

---

## 📋 Requirements

| Requirement | Version |
| ----------- | ------- |
| PHP         | ^8.0    |

---

## 🚀 Installation

```bash
composer require KYNetCode/wpzylos-cli-core
```

---

## 📖 Quick Start

### Basic Stub Compilation

```php
use WPZylos\Framework\Cli\Core\StubCompiler;
use WPZylos\Framework\Cli\Core\FileWriter;

// Create compiler with stubs directory
$compiler = new StubCompiler('/path/to/stubs');

// Compile a stub with replacements
$content = $compiler->compile('controller', [
    'namespace' => 'MyPlugin\\Http\\Controllers',
    'class'     => 'Product',
    'view'      => 'products',
]);

// Write to file
$writer = new FileWriter();
$writer->write('/path/to/ProductController.php', $content);
```

### Plugin Context Compilation

```php
// Compile with plugin-specific tokens
$content = $compiler->compileForPlugin(
    'controller',
    slug: 'my-plugin',
    prefix: 'myplugin_',
    textDomain: 'my-plugin',
    namespace: 'MyPlugin',
    extra: ['class' => 'Product', 'view' => 'products']
);
```

---

## 📁 Package Structure

```
wpzylos-cli-core/
├── src/
│   ├── StubCompiler.php    # Template compilation
│   ├── FileWriter.php      # File writing utilities
│   └── Generator.php       # Abstract generator base
├── stubs/
│   ├── controller.stub     # Controller template
│   ├── migration.stub      # Migration template
│   └── request.stub        # Request template
├── tests/                  # PHPUnit tests
└── docs/                   # Documentation
```

---

## 🏗️ Core Components

### StubCompiler

Compiles stub templates by replacing `{{token}}` placeholders with values.

```php
$compiler = new StubCompiler('/path/to/stubs');

// Set default replacements (applied to all compilations)
$compiler->setDefaults([
    'namespace' => 'MyPlugin',
    'textDomain' => 'my-plugin',
]);

// Compile with additional replacements
$content = $compiler->compile('controller', [
    'class' => 'UserController',
]);

// Get available stub names
$stubs = $compiler->getAvailable(); // ['controller', 'migration', 'request']
```

**Methods:**

| Method                                                                     | Description                            |
| -------------------------------------------------------------------------- | -------------------------------------- |
| `compile($stubName, $replacements)`                                        | Compile a stub with token replacements |
| `compileForPlugin($stub, $slug, $prefix, $textDomain, $namespace, $extra)` | Compile with plugin context tokens     |
| `setDefaults($defaults)`                                                   | Set default token values               |
| `getAvailable()`                                                           | List available stub names              |

### FileWriter

Safe file writing with automatic directory creation.

```php
$writer = new FileWriter(overwrite: false, dirPermissions: 0755);

// Write file (throws if exists and overwrite is false)
$writer->write('/path/to/File.php', $content);

// Write only if file doesn't exist
$written = $writer->writeIfNotExists('/path/to/File.php', $content);

// Enable overwrite mode
$writer->setOverwrite(true);
```

**Methods:**

| Method                              | Description                      |
| ----------------------------------- | -------------------------------- |
| `write($path, $content)`            | Write content to file            |
| `writeIfNotExists($path, $content)` | Write only if file doesn't exist |
| `setOverwrite($overwrite)`          | Set overwrite mode               |

### Generator (Abstract)

Base class for building custom file generators.

```php
use WPZylos\Framework\Cli\Core\Generator;

class ControllerGenerator extends Generator
{
    public function generate(string $name, array $options = []): array
    {
        $className = $this->toClassName($name);

        $content = $this->compiler->compile('controller', [
            'class' => $className,
            'namespace' => $options['namespace'] ?? 'App\\Controllers',
        ]);

        $path = $this->getOutputPath($name);
        $this->writer->write($path, $content);

        return [$path];
    }

    protected function getStubName(): string
    {
        return 'controller';
    }

    protected function getOutputPath(string $name): string
    {
        return $this->basePath . '/app/Http/Controllers/' . $this->toClassName($name) . 'Controller.php';
    }
}
```

**Helper Methods:**

| Method                  | Description                     |
| ----------------------- | ------------------------------- |
| `toClassName($name)`    | Convert `my-thing` to `MyThing` |
| `toVariableName($name)` | Convert `my-thing` to `myThing` |

---

## 📝 Creating Stubs

Stubs are template files with `.stub` extension using `{{token}}` placeholders:

```php
<?php
// stubs/service.stub

namespace {{namespace}}\Services;

class {{class}}Service
{
    public function __construct()
    {
        // Service for {{slug}}
    }
}
```

**Common Tokens:**

| Token            | Description      | Example     |
| ---------------- | ---------------- | ----------- |
| `{{namespace}}`  | PHP namespace    | `MyPlugin`  |
| `{{class}}`      | Class name       | `Product`   |
| `{{slug}}`       | Plugin slug      | `my-plugin` |
| `{{prefix}}`     | Database prefix  | `myplugin_` |
| `{{textDomain}}` | Text domain      | `my-plugin` |
| `{{Slug}}`       | PascalCase slug  | `MyPlugin`  |
| `{{PREFIX}}`     | Uppercase prefix | `MYPLUGIN_` |

---

## 🧪 Testing

```bash
# Run all tests
composer test

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/
```

---

## 🔒 Security

When generating files from user input:

```php
// ✅ Validate class names
$name = preg_replace('/[^a-zA-Z0-9_]/', '', $input);

// ✅ Validate paths
$realPath = realpath($targetDir);
if (!str_starts_with($outputPath, $realPath)) {
    throw new \InvalidArgumentException('Invalid path');
}
```

---

## 📦 Related Packages

| Package                                                                      | Description                                  |
| ---------------------------------------------------------------------------- | -------------------------------------------- |
| [wpzylos-cli-devtool](https://github.com/KYNetCode/wpzylos-cli-devtool) | Development commands (make:controller, etc.) |
| [wpzylos-wp-cli](https://github.com/KYNetCode/wpzylos-wp-cli)           | WP-CLI integration                           |
| [wpzylos-core](https://github.com/KYNetCode/wpzylos-core)               | Application foundation                       |

---

## 📖 Documentation

For comprehensive documentation, tutorials, and API reference, visit **[wpzylos.com](https://wpzylos.com)**.

---

## ☕ Support the Project

- [GitHub Sponsors](https://github.com/sponsors/KYNetCode)
- [PayPal Donate](https://www.paypal.com/donate/?hosted_button_id=66U4L3HG4TLCC)

---

## 📄 License

MIT License. See [LICENSE](LICENSE) for details.

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

---

**Made with ❤️ by [KYNetCode](https://github.com/KYNetCode)**
