<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Reusable logic for commands that take --only-bundles and --exclude-bundles options.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BundleFilter
{
    /**
     * Add --only-bundles and --exclude-bundles options to a command definition.
     *
     * @param Command $command
     */
    public static function addOptions(Command $command)
    {
        $command->addOption(
            'only-bundles',
            'o',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Only use resources in the given bundles'
        )->addOption(
            'exclude-bundles',
            'x',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            "Don't use resources in the given bundles"
        );
    }

    /**
     * Filter an array of bundles using options given to the input.
     * --only-bundles takes priority over --exclude-bundles.
     * Defaults to all bundles.
     *
     * @param InputInterface    $input
     * @param BundleInterface[] $bundles
     *
     * @return BundleInterface[]
     */
    public static function filterBundles(InputInterface $input, array $bundles)
    {
        if ($input->getOption('only-bundles')) {
            $included = static::normaliseBundleNames($input->getOption('only-bundles'));

            return array_values(array_filter($bundles, function ($bundle) use ($included) {
                return in_array(strtolower($bundle->getName()), $included);
            }));
        }

        if ($input->getOption('exclude-bundles')) {
            $excluded = static::normaliseBundleNames($input->getOption('exclude-bundles'));

            return array_values(array_filter($bundles, function ($bundle) use ($excluded) {
                return !in_array(strtolower($bundle->getName()), $excluded);
            }));
        }

        return $bundles;
    }

    /**
     * Filter an array of bundles using options given to the input and return their names.
     * --only-bundles takes priority over --exclude-bundles.
     * Defaults to all bundles.
     *
     * @param InputInterface    $input
     * @param BundleInterface[] $bundles
     *
     * @return BundleInterface[]
     */
    public static function filterBundleNames(InputInterface $input, array $bundles)
    {
        return array_map(function ($bundle) {
            return $bundle->getName();
        }, static::filterBundles($input, $bundles));
    }

    private static function normaliseBundleNames(array $bundleNames)
    {
        return array_map(function ($name) {
            $name = strtolower($name);
            if (substr($name, -6) !== 'bundle') {
                $name .= 'bundle';
            }

            return str_replace('_', '', $name);
        }, $bundleNames);
    }
}
