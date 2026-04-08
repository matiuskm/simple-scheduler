<?php

test('the application redirects to the admin login', function () {
    $response = $this->get('/');

    $response->assertRedirect('admin/login');
});
