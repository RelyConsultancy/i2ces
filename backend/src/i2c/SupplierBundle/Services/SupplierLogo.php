<?php

namespace i2c\SupplierBundle\Services;

use Doctrine\DBAL\Connection;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class SupplierLogo
 *
 * @package i2c\SupplierBundle\Services
 */
class SupplierLogo
{
    protected $connection;

    /**
     * SupplierLogo constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $businessUnitId
     * @param string $sourcePath
     * @param string $destinationPath
     */
    public function createLogoHardLink($businessUnitId, $sourcePath, $destinationPath)
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($destinationPath)) {
            $filesystem->mkdir($destinationPath, 0755);
        }

        $supplierLogoName = $this->getSupplierLogoName($businessUnitId);

        if (false == $supplierLogoName) {
            return;
        }

        $source = sprintf('%s/%s', $sourcePath, $this->getAttachmentLogoName($businessUnitId));
        $destination = sprintf('%s/%s', $destinationPath, $supplierLogoName);

        $filesystem->remove($destination);

        link(
            $source,
            $destination
        );
    }

    /**
     * @param string $businessUnitId
     *
     * @return string
     */
    public function getSupplierLogoName($businessUnitId)
    {
        $query = sprintf(
            'SELECT a.original_filename FROM oro_attachment_file a
             JOIN oro_business_unit b ON (a.id = b.supplier_logo_id) WHERE b.id = \'%s\'',
            $businessUnitId
        );

        return $this->connection->fetchColumn($query);
    }

    /**
     * @param string $businessUnitId
     *
     * @return mixed
     */
    public function getLogoLabel($businessUnitId)
    {
        $query = sprintf(
            'SELECT DISTINCT brand FROM i2c_evaluation
             WHERE business_unit_id = \'%s\'',
            $businessUnitId
        );

        return $this->connection->fetchColumn($query);
    }

    /**
     * @param string $businessUnitId
     *
     * @return string
     */
    protected function getAttachmentLogoName($businessUnitId)
    {
        $query = sprintf(
            'SELECT a.filename FROM oro_attachment_file a
             JOIN oro_business_unit b ON (a.id = b.supplier_logo_id) WHERE b.id = \'%s\'',
            $businessUnitId
        );

        return $this->connection->fetchColumn($query);
    }
}
