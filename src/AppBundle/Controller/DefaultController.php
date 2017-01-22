<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/shows/{numPage}", name="shows", requirements={"id" = "\d+"}, defaults={"numPage" = 1})
     * @Template()
     */
    public function showsAction(Request $request, $numPage)
    {
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:TVShow');

        if ($request->getMethod() == 'POST') {
            $value = $_POST['search'];

            $query = $em->createQuery(
                'SELECT ts
                FROM AppBundle:TVShow ts
                WHERE ts.name like :value
                OR ts.synopsis like :value'
            )->setParameter('value', '%'.$value.'%');           

            return [ 'shows' => $query->getResult() ];
        }

        $repo =  $em->createQuery(
                'SELECT ts
                FROM AppBundle:TVShow ts'
            );

        $firstResult = ($numPage-1)*6;

        $repo->setFirstResult($firstResult);
        $repo->setMaxResults(6);        

        $paginatedRepo = new Paginator($repo);

        $nbPage = ceil(count($paginatedRepo)/6);

        return [ 'shows' => $paginatedRepo, 'nbPage' => $nbPage ];
    }

    /**
     * @Route("/show/{id}", name="show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:TVShow');

        return [
            'show' => $repo->find($id)
        ];        
    }

    /**
     * @Route("/calendar", name="calendar")
     * @Template()
     */
    public function calendarAction()
    {

        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:TVShow');
        $value = new \DateTime('now');

        $query = $em->createQuery(
            'SELECT e
            FROM AppBundle:Episode e
            WHERE e.date >= :value'
        )->setParameter('value', $value);           

        return [ 'future' => $query->getResult() ];
    }

    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction()
    {
        return [];
    }
}
