# Drupal 8 Migrate API example

Enable the required modules:
- Content translation
- Languages

Add two new languages: German (de) and Dutch (nl)

Enable the module:
```drush en migrate_api_example```

Run the group import:
```drush mim --group=migrate_api_example --feedback```