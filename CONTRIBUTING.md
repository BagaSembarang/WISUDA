# Contributing to Sistem Manajemen Wisuda

Terima kasih atas minat Anda untuk berkontribusi! Berikut adalah panduan untuk berkontribusi pada proyek ini.

## Cara Berkontribusi

### Melaporkan Bug

1. Pastikan bug belum dilaporkan sebelumnya
2. Buat issue baru dengan label "bug"
3. Sertakan informasi:
   - Deskripsi bug
   - Langkah untuk reproduce
   - Expected behavior
   - Actual behavior
   - Screenshot (jika ada)
   - Environment (PHP version, MySQL version, browser)

### Mengusulkan Fitur Baru

1. Buat issue dengan label "enhancement"
2. Jelaskan fitur yang diusulkan
3. Jelaskan use case dan manfaatnya
4. Diskusikan dengan maintainer sebelum mulai coding

### Pull Request

1. Fork repository
2. Buat branch baru: `git checkout -b feature/nama-fitur`
3. Commit changes: `git commit -am 'Add some feature'`
4. Push ke branch: `git push origin feature/nama-fitur`
5. Submit pull request

## Coding Standards

### PHP

- Follow PSR-12 coding standard
- Use meaningful variable and function names
- Add comments for complex logic
- Use type hints where possible
- Keep functions small and focused

### Database

- Use prepared statements for all queries
- Add proper indexes
- Use transactions for multiple operations
- Follow naming conventions (snake_case)

### Frontend

- Use Bootstrap classes consistently
- Keep JavaScript modular
- Add comments for complex logic
- Ensure responsive design
- Test on multiple browsers

### Security

- Never commit sensitive data (passwords, API keys)
- Validate all user inputs
- Escape all outputs
- Use HTTPS in production
- Keep dependencies updated

## Testing

- Test all new features
- Test on different browsers
- Test on mobile devices
- Test with different user roles
- Test edge cases

## Documentation

- Update README.md if needed
- Update CHANGELOG.md
- Add inline comments
- Update API documentation
- Create user guide if needed

## Git Commit Messages

- Use present tense ("Add feature" not "Added feature")
- Use imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit first line to 72 characters
- Reference issues and pull requests

Examples:
```
Add user management feature
Fix QR code generation bug
Update documentation for installation
Refactor database connection class
```

## Code Review Process

1. All submissions require review
2. Maintainer will review within 3-5 days
3. Address review comments
4. Once approved, maintainer will merge

## Questions?

Feel free to ask questions by creating an issue with label "question".

Thank you for contributing! 🎓
