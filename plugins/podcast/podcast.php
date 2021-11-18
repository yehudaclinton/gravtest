<?php
namespace Grav\Plugin;

use Grav\Common\Grav;
use Grav\Common\Page\Header;
use Grav\Common\Page\Interfaces\PageInterface;
use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;
use RocketTheme\Toolbox\File\File;
use Symfony\Component\Yaml\Yaml;
use Grav\Plugin\GetID3Plugin;

/**
 * Class PodcastPlugin
 * 
 * @package Grav\Plugin
 */
class PodcastPlugin extends Plugin
{
    /** @var array */
    public $features = [
        'blueprints' => 0, // Use priority 0
    ];

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *         that the plugin wants to listen to. The key of each
     *         array section is the event that the plugin listens to
     *         and the value (in the form of an array) contains the
     *         callable (or function) as well as the priority. The
     *         higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // If in an Admin page.
        if ($this->isAdmin()) {
            $this->enable([
                'onGetPageTemplates' => ['onGetPageTemplates', 0],
                'onAdminSave' => ['onAdminSave', 0],
            ]);
            return;
        }
        // If not in an Admin page.
        $this->enable([
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 1],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
        ]);
    }

    /**
     * Add blueprint directory to page templates.
     */
    public function onGetPageTemplates(Event $event)
    {
        $types = $event->types;
        $locator = Grav::instance()['locator'];
        $types->scanBlueprints($locator->findResource('plugin://' . $this->name . '/blueprints'));
        $types->scanTemplates($locator->findResource('plugin://' . $this->name . '/templates'));
    }

    /**
     * Add templates directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onTwigSiteVariables()
    {
        $this->grav['assets']
            ->addCss('plugin://podcast/assets/css/podcast.css');
    }

    /**
     * Modifies the page header being saved to include getID3 metadata.
     */
    public function onAdminSave($event)
    {
        $page = $event['object'];

        // Process only onAdminSave events on pages.
        if (!$page instanceof PageInterface) {
            return;
        }
        $header = $page->header();

        if ( str_starts_with($page->template(), 'podcast-') ) {
            // Set autodate field on all podcast-* page types.
            if (!isset($header->date)){
                $date = date($this->grav['config']->get('system.pages.dateformat.default', 'H:i d-m-Y'));
                $header['date'] = $date;
            }

            // Fix for Feed plugin 1.8.2+ requiring 'content' instead of 'feed' in the header.
            if ( isset($header['feed']) == true ) {
                $header['content'] = $header['feed'];
                $header->undef('feed');
            }

        }

        // Return with just updated header content if not podcast-episode.
        if ($page->template() != 'podcast-episode') {
            $header = new Header((array)$header);
            return;
        }

        // Use local file for meta calculations, if present.
        // Else, use remote file for meta, if present.
        // Else, cleanup media entry in markdown header.

        if (isset($header->podcast['audio']['local']['select'])) {
            $local['select'] = $header->podcast['audio']['local']['select'];
            $media = $page->media()->audios()[$local['select']];

            // Create array for backawards compatability with Grav content created with < v1.7.
            $audio = $header->podcast['audio'];
            $file_path = $media->relativePath();
            $file_url = $page->getRoute()->getRootPrefix();
            $file_url .= DS . implode('/', $page->getRoute()->getRouteParts());
            $file_url .= DS . $local['select'];

            $audio_meta['guid'] = $file_url;
            $audio_meta['type'] = $this->retreiveAudioType($file_path);
            $audio_meta['duration'] = $this->retreiveAudioDuration($file_path);
            $audio_meta['enclosure_length'] = filesize($file_path);

            $local_file = [
                'name' => $local['select'],
                'type' => $audio_meta['type'],
                'size' => $audio_meta['enclosure_length'],
                'path' => $file_url,
            ];
            $local[$file_path] = $local_file;
            $header->offsetSet('podcast.audio.local', $local);
        }
        if (isset($header->podcast['audio']['remote']) && !isset($audio_meta)) {
            // Download fle from external url to temporary location.
            $path = $this->getRemoteAudio($header->podcast['audio']['remote']);

            if ($path) {
                $audio_meta['guid'] = $header->podcast['audio']['remote'];
                $audio_meta['type'] = $this->retreiveAudioType($path);
                $audio_meta['duration'] = $this->retreiveAudioDuration($path);
                $audio_meta['enclosure_length'] = $this->retreiveAudioLength($path);

                // Delete temporary remote file.
                unlink($path);
            } else {
                // Remove previously calculated meta if remote file is not found.
                unset($header->podcast['audio']['meta']);
            }
        }

        // Reset the guid if using an external file source.
        if (isset($header->podcast['audio']['remote']) && isset($header->podcast['audio']['meta'])) {
            $audio_meta['guid'] = $header->podcast['audio']['remote'];
        }

        // Prepare $page to return new header data.
        if (isset($audio_meta)) {
            $header->set('podcast.audio.meta', $audio_meta);
        } else {
            // Cleanup any leftover data if neither local or remote file are set.
            $header->undef('podcast.audio');
        }

        $header = new Header((array)$header);
        return;
    }

    /**
     * Retrieve audio metadata filesize.
     *
     * @param string $file
     *    Path to audio file.
     * @return type
     *    Audio filesize
     */
    public static function retreiveAudioLength($file)
    {
        $id3 = GetID3Plugin::analyzeFile($file);
        return ($id3['filesize']);
    }

    /**
     * Retrieve audio metadata filetype.
     *
     * @param string $file
     *    Path to audio file.
     * @return type
     *    Audio filesize
     */
    public static function retreiveAudioType($file)
    {
        $id3 = GetID3Plugin::analyzeFile($file);
        return ($id3['mime_type']);
    }

    /**
     * Retrieve audio metadata duration.
     *
     * @param string $file
     *     Path to audio file.
     * @return string
     *     Audio file duration.
     */
    public static function retreiveAudioDuration($file)
    {
        $id3 = GetID3Plugin::analyzeFile($file);
        return ($id3['playtime_string']);
    }

    /**
     * Retrieve audio from remote source.
     *
     * @param string $url
     *     http(s) path to audio file.
     * @return string
     *     filepath to temp file.
     */
    public function getRemoteAudio($url)
    {
        // Make sure the url is reachable.
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // $retcode >= 400 -> not found; 200 -> found; 0 -> server not found.
        if ($retcode >= 400 || $retcode == 0) {
            $grav_messages = $this->grav['messages'];
            $grav_messages->add("HTTP Error ($retcode) while attempting to locate remote file '$url' .", 'error');
            $grav_messages->add("Audio File metadata calculation failed!", 'error');
            return null;
        }

        // Download file to temp location.
        if ($remote_file = fopen($url, 'rb')) {
            $local_file = tempnam('/tmp', 'podcast');
            $handle = fopen($local_file, "w");
            $contents = stream_get_contents($remote_file);

            fwrite($handle, $contents);
            fclose($remote_file);
            fclose($handle);

            return $local_file;
        }
    }

    /**
     * Finds list of available iTunes categories.
     *
     * @return array
     *     Array of options for select list.
     */
    public static function getCategoryOptions()
    {
        $options = array();
        $data_file_path = __DIR__ . DS . 'data' . DS . 'iTunesCategories.yaml';
        $file = File::instance($data_file_path);
        $data = Yaml::parse($file->content());
        $keys = array_keys($data);

        foreach ($keys as $option) {
            $options[$option] = $option;
        }

        return $options;
    }

    /**
     * Finds list of available iTunes subcategories.
     *
     * @return array
     *     Array of options for select list.
     */
    public static function getSubCategoryOptions()
    {
        $options = array();
        $data_file_path = __DIR__ . DS . 'data' . DS . 'iTunesCategories.yaml';
        $file = File::instance($data_file_path);
        $data = Yaml::parse($file->content());
        foreach ($data as $key => $category) {
            if ($category != null) {
                foreach ($category as $sub) {
                    $options[$sub] = $sub;
                }
            } else {
                $options[$key] = $key;
            }
        }

        return $options;
    }

    /**
     * Finds list of available language options.
     *
     * @return array
     *     Array of options for select list.
     */
    public static function getLanguageOptions()
    {
        $options = array();
        $data_file_path = __DIR__ . DS . 'data' . DS . 'languages.yaml';
        $file = File::instance($data_file_path);
        $languages = Yaml::parse($file->content());
        foreach ($languages as $language) {
            $options[$language['ISO 639 Code']] = $language['English Name'] . " (" . $language['ISO 639 Code'] . ")";
        }
        return $options;
    }

    /**
     * Returns property of current user for admin blueprints.
     *
     * @return string
     *     Property of the currently signed in user.
     */
    public static function getCurrentUserInfo($property)
    {
        if (!$property) {
            return "";
        }
        $grav = Grav::instance();
        return $grav['user']->get($property);
    }
}
