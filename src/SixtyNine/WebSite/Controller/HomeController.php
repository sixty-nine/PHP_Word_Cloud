<?php

namespace SixtyNine\WebSite\Controller;

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use SixtyNine\WordCloud\Builder\WordCloudBuilder;
use SixtyNine\WordCloud\Renderer\WordCloudRenderer;
use SixtyNine\WordCloud\Helper\Palette;
use SixtyNine\WordCloud\FrequencyTable\FrequencyTableFactory;
use SixtyNine\WordCloud\Builder\Context\BuilderContextFactory;
use SixtyNine\WordCloud\ImageBuilder\RawImageRenderer;

class HomeController
{
    public function __construct(Application $app)
    {
        $app->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../Resources/views',
        ));
    }

    public function indexAction(Request $request, Application $app)
    {
        $data = array(
            'url' => 'https://en.wikipedia.org/wiki/Wikipedia',
            'palette' => 'aqua',
            'font' => 'Arial.ttf',
            'orientation' => WordCloudBuilder::WORDS_MAINLY_HORIZONTAL,
        );
        $form = $app['form.factory']->createBuilder('form', $data)
            ->add('url', 'text', array(
                'required' => false,
                'attr' => array('placeholder' => 'URL')
            ))
            ->add('palette', 'choice', array(
                'choices' => array(
                    'aqua' => 'Aqua',
                    'yellow/blue' => 'Yellow/Blue',
                    'grey' => 'Greyscale',
                    'brown' => 'Brown',
                    'army' => 'Army',
                    'pastel' => 'Pastel',
                    'red' => 'Red',
                ),
            ))
            ->add('font', 'choice', array(
                'choices' => array(
                    'Airmole_Antique.ttf' => 'Airmole Antique',
                    'Airmole_Shaded.ttf' => 'Airmole Shaded',
                    'Alexis_3D.ttf' => 'Alexis 3D',
                    'Almonte_Snow.ttf' => 'Almonte Snow',
                    'Arial.ttf' => 'Arial',
                    'Paper_Cut.ttf' => 'Paper Cut',
                    'TheThreeStoogesFont.ttf' => 'The Three Stooges',
                ),
            ))
            ->add('orientation', 'choice', array(
                'choices' => array(
                    WordCloudBuilder::WORDS_HORIZONTAL => 'Horizontal',
                    WordCloudBuilder::WORDS_MAINLY_HORIZONTAL => 'Mainly horizontal',
                    WordCloudBuilder::WORDS_MIXED => 'Mixed',
                    WordCloudBuilder::WORDS_MAINLY_VERTICAL => 'Mainly vertical',
                    WordCloudBuilder::WORDS_VERTICAL => 'Vertical',
                ),
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
        }
        $imgRenderer = $this->createRenderer($data);

        return $app['twig']->render('index.html.twig', array(
            'image' => base64_encode($imgRenderer->getImage()),
            'form' => $form->createView(),
        ));
    }

    protected function createRenderer($data)
    {
        $url = $data['url'];
        $text = file_get_contents($url);
        $d = new \DOMDocument;
        $mock = new \DOMDocument;
        $d->loadHTML($text);
        $body = $d->getElementsByTagName('body')->item(0);
        foreach ($body->childNodes as $child) {
            $mock->appendChild($mock->importNode($child, true));
        }
        $text = html_entity_decode(strip_tags($mock->saveHTML()));

        $img_width = 800;
        $img_height = 640;

        $palette = Palette::getNamedPalette($data['palette']);
        $font = ROOT . '/fonts/' . $data['font'];

        $ft = FrequencyTableFactory::getDefaultFrequencyTable($text);

        $builder = new WordCloudBuilder(
            $ft,
            BuilderContextFactory::getDefaultBuilderContext($ft, $palette, $font, $img_width, $img_height),
            array(
                'font' => $font,
                'size' => array($img_width, $img_height)
            )
        );

        $imgRenderer = new RawImageRenderer(
            $builder->build(50, null, $data['orientation']),
            new WordCloudRenderer()
        );

        return $imgRenderer;
    }
}