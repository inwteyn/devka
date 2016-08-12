--
-- Update module Updates
--

UPDATE `engine4_core_modules` SET `version` = '4.1.4'  WHERE `name` = 'updates';

INSERT INTO `engine4_updates_widgets` (`id`, `name`, `title`, `module`, `last_sent_id`, `description`, `params`, `structure`, `blacklist`) VALUES
(25, 'mixed_recommendation', 'Mixed Recommendation', 'suggest', 0, 'description', '{"title":"Mixed Recommendations","count":"6"}', 'suggest_table', NULL),
(26, 'recommended_members', 'Recommended Members', 'suggest', 0, 'description', '{"title":"Recommended Members","count":"6"}', 'suggest_table', NULL),
(27, 'recommended_pages', 'Recommended Pages', 'suggest', 0, 'description', '{"title":"Recommended Pages","count":"6"}', 'suggest_table', NULL);