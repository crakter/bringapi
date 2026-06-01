# Security policy

## Supported versions

| Version       | Status               | Security fixes |
|---------------|----------------------|----------------|
| 4.x (preview) | Active development   | Yes            |
| 3.x           | Maintenance          | Critical only  |
| < 3.0         | End of life          | No             |

## Reporting a vulnerability

**Please do not file a public GitHub issue for security problems.** Reach
out privately to **crakter@gmail.com** with:

1. A description of the vulnerability and its impact (what an attacker
   can do).
2. Reproduction steps — ideally a minimal PHP snippet or a packet
   capture.
3. The affected version range (composer.lock excerpt is fine).
4. Any suggested fix, if you have one.

You will get an acknowledgement within **5 working days**. We aim to ship
a fix or a public advisory within **30 days** of confirmation.

If you'd prefer encrypted email, request a PGP key in your first message.

## Scope

In scope:
- Vulnerabilities in the library's own code (request building, response
  parsing, header handling, credential storage, XML/JSON deserialisation).
- Credential leakage through logging, exception messages, or debug dumps.

Out of scope:
- Vulnerabilities in Bring's own servers (report those to Bring directly
  via the contact form on https://developer.bring.com).
- Issues that require a malicious local environment (e.g. an attacker
  who can already modify your `.env`).
- Vulnerabilities in transitive dependencies — please report those
  upstream first; we will ship a coordinated bump once a CVE is public.

## Credentials hygiene

This library treats the Bring API key as a sensitive parameter:

- `Bring\Api\Auth\Credentials` constructor parameter is annotated with
  `#[\SensitiveParameter]` (PHP 8.2+ scrubs it from stack traces).
- `__debugInfo()` masks the key — `print_r($credentials)` outputs a
  SHA-256 fingerprint, never the raw value.
- `Bring\Api\Exception\BringApiException::getMessage()` does not embed
  the raw response body (Bring occasionally echoes credentials in error
  envelopes). The full PSR-7 response remains reachable via
  `getResponse()` for callers that explicitly need it.
- `Bring\Api\Logging\RedactingLogger` wraps any PSR-3 logger and strips
  the Mybring auth headers and the literal API key from messages and
  context.

If you find a path that bypasses any of these, treat it as a security
issue and email the address above.
