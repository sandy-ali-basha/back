-- Add 3 optional localized video columns to home_section_items.
ALTER TABLE home_section_items
    ADD COLUMN video_en VARCHAR(255) NULL AFTER image,
    ADD COLUMN video_ar VARCHAR(255) NULL AFTER video_en,
    ADD COLUMN video_kr VARCHAR(255) NULL AFTER video_ar;

-- Optional cleanup: remove any videos from non-reels sections (reels section id = 4).
UPDATE home_section_items
SET video_en = NULL,
    video_ar = NULL,
    video_kr = NULL
WHERE home_section_id <> 4;
