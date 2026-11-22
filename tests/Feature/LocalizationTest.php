<?php

use App\Models\Shortener;
use App\Models\User;

it('chooses a supported browser language on the first request', function (?string $header, string $locale, string $tagline) {
    config(['app.locale' => 'en']);

    $headers = $header === null ? [] : ['Accept-Language' => $header];
    $this->get('/', $headers)
        ->assertOk()
        ->assertSee('<html lang="'.$locale.'">', false)
        ->assertSee($tagline)
        ->assertSessionHas('locale', $locale);
})->with([
    'regional German' => ['de-AT,de;q=0.9,en;q=0.8', 'de', 'Erstelle kurze Links für deine langen URLs'],
    'preferred Italian' => ['en;q=0.4,it-IT;q=0.9,de;q=0.7', 'it', 'Crea link brevi per i tuoi URL lunghi'],
    'unsupported preferred language' => ['fr-FR,fr;q=0.9,en-US;q=0.8', 'en', 'Create short links for your long URLs'],
    'rejected language' => ['de;q=0,it;q=0.7', 'it', 'Crea link brevi per i tuoi URL lunghi'],
    'only unsupported language' => ['fr-FR', 'en', 'Create short links for your long URLs'],
    'missing header' => [null, 'en', 'Create short links for your long URLs'],
]);

it('keeps an explicitly selected language despite browser preferences on later requests', function () {
    $this->get('/', ['Accept-Language' => 'de'])->assertSessionHas('locale', 'de');

    $this->from('/')->post(route('language.update'), ['locale' => 'it'])
        ->assertSessionHas('locale', 'it');

    $this->get('/', ['Accept-Language' => 'en'])
        ->assertOk()
        ->assertSee('<html lang="it">', false)
        ->assertSee('Crea link brevi per i tuoi URL lunghi');
});

it('uses the configured default when no browser language is supported', function () {
    config(['app.locale' => 'de']);

    $this->get('/', ['Accept-Language' => 'fr,es;q=0.8'])
        ->assertOk()
        ->assertSee('<html lang="de">', false)
        ->assertSessionHas('locale', 'de');
});

it('persists the selected language and supplies translations to the page and API', function (string $locale, string $tagline, string $create, string $validation) {
    $this->from('/')->post(route('language.update'), ['locale' => $locale])
        ->assertRedirect('/')
        ->assertSessionHas('locale', $locale);

    $this->get('/')
        ->assertOk()
        ->assertSee('<html lang="'.$locale.'">', false)
        ->assertSee($tagline)
        ->assertSee($create);

    $this->postJson(route('shortener.store'), [])
        ->assertUnprocessable()
        ->assertJsonPath('errors.url.0', $validation);
})->with([
    ['de', 'Erstelle kurze Links für deine langen URLs', 'Shortlink erstellen', 'Eine URL ist erforderlich.'],
    ['it', 'Crea link brevi per i tuoi URL lunghi', 'Crea link breve', 'È richiesto un URL.'],
    ['en', 'Create short links for your long URLs', 'Create short link', 'A URL is required.'],
]);

it('rejects unsupported languages without changing the session language', function () {
    $this->withSession(['locale' => 'it'])
        ->from('/')
        ->post(route('language.update'), ['locale' => 'fr'])
        ->assertRedirect('/')
        ->assertSessionHasErrors('locale')
        ->assertSessionHas('locale', 'it');

    $this->get('/')->assertSee('<html lang="it">', false);
});

it('keeps the language when signing out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->withSession(['locale' => 'it'])
        ->post(route('logout'))
        ->assertRedirect('/')
        ->assertSessionHas('locale', 'it');

    $this->get('/')->assertSee('<html lang="it">', false);
});

it('localizes built-in validation errors for the QR code endpoint', function () {
    $this->withSession(['locale' => 'it'])
        ->postJson(route('qr-code.generate'), [])
        ->assertUnprocessable()
        ->assertJsonPath('errors.text.0', 'Il campo testo è obbligatorio.');
});

it('replaces validation parameters in the selected language', function () {
    $this->withSession(['locale' => 'it'])
        ->postJson(route('shortener.store'), [
            'url' => 'https://example.com',
            'description' => str_repeat('a', 1025),
        ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.description.0', 'La descrizione può contenere al massimo 1024 caratteri.');
});

it('provides the German edit dialog title to the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['locale' => 'de'])
        ->get(route('shortener.dashboard'))
        ->assertOk()
        ->assertSee('\u0022edit_shortener\u0022:\u0022Shortlink bearbeiten\u0022', false);
});

it('renders localized short link error pages', function (int $status, string $locale, string $title, bool $authenticated) {
    $this->freezeTime();

    if ($status === 409) {
        Shortener::factory()->futurePublish()->create(['Hash' => 'error-link']);
    } elseif ($status === 410) {
        Shortener::factory()->create(['Hash' => 'error-link', 'ExpirationDate' => now()->subDay()]);
    }

    if ($authenticated) {
        $this->actingAs(User::factory()->create());
    }

    $this->withSession(['locale' => $locale])
        ->get('/error-link')
        ->assertStatus($status)
        ->assertSee('<html lang="'.$locale.'">', false)
        ->assertSee($title);
})->with([
    '404 German' => [404, 'de', 'Shortlink nicht gefunden', false],
    '404 English' => [404, 'en', 'Short link not found', false],
    '404 Italian' => [404, 'it', 'Link breve non trovato', false],
    '409 German' => [409, 'de', 'Shortlink noch nicht verfügbar', false],
    '409 English' => [409, 'en', 'Short link not yet available', false],
    '409 Italian' => [409, 'it', 'Link breve non ancora disponibile', false],
    '410 German' => [410, 'de', 'Shortlink ist abgelaufen', false],
    '410 English' => [410, 'en', 'Short link has expired', false],
    '410 Italian' => [410, 'it', 'Link breve scaduto', false],
    '404 authenticated' => [404, 'de', 'Shortlink nicht gefunden', true],
    '410 authenticated' => [410, 'de', 'Shortlink ist abgelaufen', true],
]);
