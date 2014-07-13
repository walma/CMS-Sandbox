<?php

namespace SmartCore\Bundle\CMSBundle\Controller;

use Knp\RadBundle\Controller\Controller;
use SmartCore\Bundle\CMSBundle\Entity\Folder;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class AdminController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('cms_admin_structure'));
    }

    /**
     * @return Response
     */
    public function notFoundAction()
    {
        return $this->render('CMSBundle:Admin:not_found.html.twig', []);
    }

    /**
     * Renders Elfinder.
     *
     * @param Request $request
     * @return Response
     */
    public function elfinderAction(Request $request)
    {
        $parameters = $this->container->getParameter('fm_elfinder');

        return $this->render('CMSBundle:Admin:elfinder.html.twig', [
            'locale' => $parameters['locale'] ? $parameters['locale'] : $request->getLocale(),
            'fullscreen' => true,
            'includeAssets' => $parameters['include_assets'],
        ]);
    }

    /**
     * Отображение списка модулей.
     *
     * @return Response
     */
    public function moduleAction()
    {
        return $this->render('CMSBundle:Admin:module.html.twig', [
            'modules' => $this->get('cms.module')->all(),
        ]);
    }

    /**
     * @param string $filename
     */
    public function moduleInstallAction($filename = null)
    {
        $finder = new Finder();

        if (is_dir($this->get('kernel')->getRootDir() . '/../dist')) {
            $finder
                ->ignoreDotFiles(false)
                ->ignoreVCS(true)
                ->name('*.zip')
                ->in($this->get('kernel')->getRootDir() . '/../dist');
        } else {
            $finder = [];
        }

        // @todo убрать в сервис.
        if ( ! empty($filename)) {
            $this->get('cms.module')->install($filename);
        }

        return $this->render('CMSBundle:Admin:module_install.html.twig', [
            'modules'  => $finder,
            'filename' => $filename,
        ]);
    }

    /**
     * AJAX обновление БД.
     *
     * @param Request $request
     * @return Response
     */
    public function moduleInstallUpdateDbAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $application = new Application($this->get('kernel'));
            $application->setAutoExit(false);
            $input = new ArrayInput(['command' => 'doctrine:schema:update', "--force" => true]);
            $output = new BufferedOutput();

            $retval = $application->run($input, $output);

            return new Response('БД успешно обновлена.<p>' . $output->fetch() . '</p>' );
        } else {
            return new Response('Обновление БД возможно только через AJAX.');
        }
    }
}
