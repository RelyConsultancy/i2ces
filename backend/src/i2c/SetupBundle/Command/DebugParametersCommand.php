<?php
/**
 * Core utility commands
 *
 * @category Core
 * @package  i2c.SetupBundle.Command
 */
namespace i2c\SetupBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Utility command to help debugging Symfony2 parameters
 *
 * @author Georgiana Gligor <gb@tekkie.ro>
 */
class DebugParametersCommand extends ContainerAwareCommand
{

    /**
     * Configuration of the current command
     */
    public function configure()
    {
        $this->setName('debug:parameters')
            ->setDescription('Displays currently defined parameters of the application')
        ;
    }

    /**
     * Executes parameter debug command
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $in
     * @param \Symfony\Component\Console\Output\OutputInterface $out
     *
     * @return integer 0 if everything went fine, or an error code
     */
    public function execute(InputInterface $in, OutputInterface $out)
    {
        $out->writeln($this->getHelper('formatter')->formatSection('router', 'Currently defined parameters'));

        $allDefinedParameters = $this->getContainer()->getParameterBag()->all();
        if (! count($allDefinedParameters)) {
            $out->writeln('No parameters defined yet!');
            exit(0);
        }

        ksort($allDefinedParameters);

        // find which name is longest; similar for value
        $parameterNameLengths = array_map('strlen', array_keys($allDefinedParameters));
        $maxDisplayValue = 0;
        foreach ($allDefinedParameters as $value) {
            if (is_array($value)) {
                $currentValueLengths = array_map('strlen', array_keys($allDefinedParameters));
                $maxCurrentValue = 3 + max($currentValueLengths);

                $maxDisplayValue = max($maxDisplayValue, $maxCurrentValue);
                continue;
            }
            $maxDisplayValue = max($maxDisplayValue, strlen($value));
        }

        $format1  = '%-'.(max($parameterNameLengths) + 19).'s %-'.($maxDisplayValue + 19).'s %s';
        $out->writeln(sprintf($format1, '<comment>Parameter</comment>', '<comment>Value</comment>', ''));

        foreach ($allDefinedParameters as $fullService => $value) {
            if (is_array($value)) {
                $outputValue = '';
                foreach ($value as $item) {
                    $outputValue .= ' * ' . serialize($item) . "\n";
                }
                continue;
            }

            $out->writeln(sprintf($format1, $fullService, $value, ''));
        }
    }

}
