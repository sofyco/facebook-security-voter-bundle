<?php declare(strict_types=1);

namespace Sofyco\Bundle\FacebookSecurityVoterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('facebook_security_voter');

        /** @var ArrayNodeDefinition $root */
        $root = $builder->getRootNode();

        $options = $root->children();
        $options->scalarNode('secret')->isRequired()->cannotBeEmpty()->end();
        $options->scalarNode('signature')->isRequired()->cannotBeEmpty()->end();

        return $builder;
    }
}
