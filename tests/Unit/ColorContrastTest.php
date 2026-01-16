<?php

use App\Support\ColorContrast;

it('chooses black text for light backgrounds', function () {
    expect(ColorContrast::textColorForBackground('#FFFFFF'))->toBe('#000000');
});

it('chooses white text for dark backgrounds', function () {
    expect(ColorContrast::textColorForBackground('#000000'))->toBe('#FFFFFF');
});
