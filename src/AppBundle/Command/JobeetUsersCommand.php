<?php
/**
 * @file
 */

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobeetUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('jobeet:users')
             ->setDescription('Add Jobeet users')
             ->addArgument('username', InputArgument::REQUIRED)
             ->addArgument('password', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $user = new User();
        $user->setUsername($username);

        $factory = $this->getContainer()->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($encodedPassword);
        $em->persist($user);
        $em->flush();

        $output->writeln(sprintf('Added %s user with password %s.', $username, $password));
    }
}
