# Implementation — Liturgical Color

Adds an optional `liturgical_color` field to schedules, allows admins to set it, and uses it to tint schedule cards in the dashboard widgets.

## Data model
- Added nullable column `schedules.liturgical_color` via migration:
  - `database/migrations/2025_12_17_000001_add_liturgical_color_to_schedules_table.php`
- Updated `Schedule` model fillable to include `liturgical_color`.

## Admin UI
- Schedule form now includes a Liturgical Color select with options:
  - hijau, merah, putih, merah muda, ungu
- Schedule admin table shows the liturgical color as a badge column.

## Dashboard display
- Both dashboard widgets tint each schedule record based on `liturgical_color`:
  - hijau → green background
  - merah → red background
  - putih → neutral/white background
  - merah muda → pink background
  - ungu → purple background

## Testing
- Factory updated to populate `liturgical_color` for sample data.
- Run: `php artisan test`

