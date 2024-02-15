<?php

test('with valid user data', function (): void {
    $user = resourceOwner();

    expect($user->getId())->toBe(123456);
    expect($user->getName())->toBe('mock_name');
    expect($user->getOwner())->toBe('mock_owner');

    expect($user->toArray())->toBe([
        'sub' => 'CHARACTER:EVE:123456',
        'name' => 'mock_name',
        'owner' => 'mock_owner',
    ]);
});

test('with invalid user data', function (): void {
    $user = resourceOwner('mock_character_id');

    expect($user->getId())->toBeEmpty();
    expect($user->getName())->toBe('mock_name');
    expect($user->getOwner())->toBe('mock_owner');
});
