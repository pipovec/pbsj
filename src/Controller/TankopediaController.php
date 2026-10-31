<?php declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TankopediaController extends AbstractController
{
    /**
     * Keys match api3 /api/tankopedia; labels and units are for this site only.
     */
    private const array QUESTIONS = [
        'max_ammo_best' => ['label' => 'Hat die meiste Munition', 'unit' => 'Stk.'],
        'max_ammo_worst' => ['label' => 'Hat die wenigste Munition', 'unit' => 'Stk.'],
        'speed_forward_best' => ['label' => 'Ist am schnellsten', 'unit' => 'km/h'],
        'speed_forward_worst' => ['label' => 'Ist am langsamsten', 'unit' => 'km/h'],
        'speed_forward_rapid' => ['label' => 'Ist im Rapid-Modus am schnellsten', 'unit' => 'km/h'],
        'speed_backward_best' => ['label' => 'Fährt am schnellsten rückwärts', 'unit' => 'km/h'],
        'speed_backward_worst' => ['label' => 'Fährt am langsamsten rückwärts', 'unit' => 'km/h'],
        'speed_backward_rapid' => ['label' => 'Fährt im Rapid-Modus am schnellsten rückwärts', 'unit' => 'km/h'],
        'max_weight' => ['label' => 'Ist am schwersten', 'unit' => 'kg'],
        'min_weight' => ['label' => 'Ist am leichtesten', 'unit' => 'kg'],
        'max_hp' => ['label' => 'Hat die meisten HP', 'unit' => 'HP'],
        'max_turret_hp' => ['label' => 'Hat die meisten Turm-HP', 'unit' => 'HP'],
        'max_engine_power' => ['label' => 'Hat den stärksten Motor', 'unit' => 'PS'],
        'max_caliber' => ['label' => 'Hat das größte Kaliber', 'unit' => 'mm'],
        'min_caliber' => ['label' => 'Hat das kleinste Kaliber', 'unit' => 'mm'],
        'min_aim_time' => ['label' => 'Zielt am schnellsten', 'unit' => 's'],
        'max_aim_time' => ['label' => 'Zielt am langsamsten', 'unit' => 's'],
        'max_fire_rate' => ['label' => 'Hat die höchste Feuerrate', 'unit' => 'Schuss/min'],
        'min_fire_rate' => ['label' => 'Hat die niedrigste Feuerrate', 'unit' => 'Schuss/min'],
        'max_hull_front_armor' => ['label' => 'Hat die stärkste Wannenfrontpanzerung', 'unit' => 'mm'],
        'max_turret_front_armor' => ['label' => 'Hat die stärkste Turmfrontpanzerung', 'unit' => 'mm'],
        'max_view_range' => ['label' => 'Hat die größte Sichtweite', 'unit' => 'm'],
        'min_view_range' => ['label' => 'Hat die kleinste Sichtweite', 'unit' => 'm'],
        'min_reload_time' => ['label' => 'Lädt am schnellsten nach', 'unit' => 's'],
        'max_reload_time' => ['label' => 'Lädt am langsamsten nach', 'unit' => 's'],
        'min_dispersion' => ['label' => 'Hat die präziseste Kanone', 'unit' => 'm'],
        'max_dispersion' => ['label' => 'Hat die ungenaueste Kanone', 'unit' => 'm'],
        'max_penetration' => ['label' => 'Hat den höchsten Durchschlag', 'unit' => 'mm'],
        'max_alpha_damage' => ['label' => 'Hat den höchsten Alpha-Schaden', 'unit' => 'HP'],
        'max_turret_traverse' => ['label' => 'Dreht den Turm am schnellsten', 'unit' => '°/s'],
        'min_turret_traverse' => ['label' => 'Dreht den Turm am langsamsten', 'unit' => '°/s'],
    ];

    private const array TANK_TYPES = [
        'lightTank' => 'Leichter Panzer',
        'mediumTank' => 'Mittlerer Panzer',
        'heavyTank' => 'Schwerer Panzer',
        'AT-SPG' => 'Jagdpanzer',
        'SPG' => 'Artillerie',
    ];

    private const array NATIONS = [
        'ussr' => 'UdSSR',
        'germany' => 'Deutschland',
        'usa' => 'USA',
        'france' => 'Frankreich',
        'uk' => 'Großbritannien',
        'china' => 'China',
        'japan' => 'Japan',
        'czech' => 'Tschechoslowakei',
        'sweden' => 'Schweden',
        'poland' => 'Polen',
        'italy' => 'Italien',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly string $api3Url,
    ) {}

    #[Route('/tankopedia', name: 'app_tankopedia', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $question = $request->query->getString('question');
        if (!array_key_exists($question, self::QUESTIONS)) {
            $question = null;
        }

        $tier = $request->query->getString('tier');
        $tier = ctype_digit($tier) && (int) $tier >= 1 && (int) $tier <= 11 ? (int) $tier : null;

        $type = $request->query->getString('type');
        if (!array_key_exists($type, self::TANK_TYPES)) {
            $type = null;
        }

        $tanks = null;
        $error = false;
        if (null !== $question) {
            try {
                $tanks = $this->httpClient->request('GET', $this->api3Url.'/api/tankopedia', [
                    'query' => array_filter(
                        ['question' => $question, 'tier' => $tier, 'type' => $type],
                        static fn ($value) => null !== $value,
                    ),
                    'timeout' => 5,
                ])->toArray();
            } catch (ExceptionInterface $e) {
                $this->logger->error('Tankopedia request to api3 failed', ['exception' => $e]);
                $error = true;
            }
        }

        return $this->render('tankopedia/index.html.twig', [
            'questions' => self::QUESTIONS,
            'tankTypes' => self::TANK_TYPES,
            'nations' => self::NATIONS,
            'question' => $question,
            'tier' => $tier,
            'type' => $type,
            'tanks' => $tanks,
            'error' => $error,
        ]);
    }
}
