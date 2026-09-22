# Portfolio assistant setup

The server supports providers implementing the OpenAI-compatible `POST /chat/completions` API, with bearer authentication and `choices[0].message.content` responses. This is one adapter compatible with multiple providers, not an adapter for every proprietary API. Choose a model that supports system messages, `temperature`, and `max_tokens`.

Configure on the server:

```dotenv
AI_PROVIDER=openai-compatible
AI_BASE_URL=https://your-provider.example/v1
AI_API_KEY=your-server-side-secret
AI_MODEL=your-provider-model-id
AI_DAILY_LIMIT=100
AI_RETENTION_DAYS=0
```

Use the provider's documented HTTPS base URL, key, and exact model identifier. The key is never included in HTML, JavaScript, logs, or errors. The default base URL is supplied in `.env.example`; no model or key is assumed. Unsupported provider values, missing keys/models, disabled chat, upstream errors, and timeouts produce a graceful unavailable response. After environment changes run `php artisan config:cache` in production.

Enable the widget in **Admin → Profile & settings**. Add verified supplementary facts under **Knowledge**, publish them, and set profile availability explicitly if you want visitors to receive an availability answer. Blank availability means unknown. Only published project, skill, experience, education, and knowledge records are sent, together with public profile fields. Hidden phone, email, social links, and location are excluded. Draft content, inbox messages, users, and credentials are never added to context.

Each question is answered independently with fresh database facts. The browser displays the conversation for convenience but does not send previous answers as trusted context. Reset clears the visible conversation and cancels a pending request. This avoids carrying earlier model mistakes into subsequent requests. Questions should be self-contained.

## Limits and defenses

- CSRF protection, eight requests per IP per minute, and a global daily request budget in the configured shared cache.
- Input capped at 1,000 characters, context at 60 KB, output at 400 tokens / 5,000 characters, and provider timeout at 25 seconds.
- Failed upstream attempts also count against the budget to avoid costly retry loops.
- Fixed server instructions treat visitor text and database text as data. The model has no tools, filesystem access, SQL access, or ability to change the site.
- Responses render with `textContent`; model HTML is never executed.
- Unknown facts must be acknowledged and visitors directed to contact Bheem.
- Prompt instructions reduce injection risk but cannot guarantee a model's truthfulness. Test the chosen model with adversarial questions before public launch. No live provider claim is made without credentials.

Transcripts are off by default. If enabled, the privacy notice displays the retention period and the scheduler prunes expired rows. Anonymous helpful/unhelpful feedback is only accepted after a successful reply; each reply permits one feedback submission. Feedback is reviewed/deleted in admin.
