<?php
declare (strict_types=1);

namespace plugin\bank\services;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ExportExcel
{
    /**
     * 导出 Excel 文件，应用指定风格
     *
     * @param string $exportFileName 导出文件的基础名称
     * @param string $title 表格标题
     * @param array $headers 列头配置 [column => ['label' => string, 'width' => int, 'field' => string, 'monospace' => bool, 'highlight' => callable, 'numberFormat' => string]]
     * @param array $exportList 要导出的数据
     * @param string $style 风格配置 (modern_minimalist, elegant_classic, vibrant_professional, soft_neutral, sleek_corporate, pastel_serenity)
     * @param string $directoryPath 保存文件的目录（默认：public_path()/excel/）
     * @return string[] 包含文件名和文件路径的数组
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportExcel(
        string $exportFileName,
        string $title,
        array $headers,
        array $exportList,
        string $style = 'modern_minimalist',
        string $directoryPath = ''
    ): array {
        // 验证输入
        if (empty($headers)) {
            throw new \Exception('列头不能为空');
        }
        if (empty($exportList)) {
            throw new \Exception('导出数据不能为空');
        }

        // 初始化 Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 获取风格配置
        $styleConfig = $this->getStyleConfig($style);

        // 应用默认样式
        $spreadsheet->getDefaultStyle()
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($styleConfig['default_background']);
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName($styleConfig['font'])
            ->setSize($styleConfig['font_size'])
            ->setColor(new Color($styleConfig['font_color']));
        $sheet->getDefaultRowDimension()->setRowHeight($styleConfig['row_height']);

        // 计算列范围
        $highestColumn = $this->getColumnLetter(count($headers) - 1);

        // 设置标题行
        $sheet->mergeCells("A1:{$highestColumn}1");
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')
            ->getFont()
            ->setBold(true)
            ->setSize($styleConfig['title_font_size'])
            ->setColor(new Color($styleConfig['title_font_color']));
        $sheet->getStyle('A1')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($styleConfig['title_background']);
        $sheet->getStyle('A1')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(false);
        $sheet->getRowDimension(1)->setRowHeight($styleConfig['title_row_height']);

        // 设置表头
        foreach ($headers as $col => $config) {
            $colLetter = $col;
            $sheet->setCellValue("{$colLetter}2", $config['label']);
            $sheet->getColumnDimension($colLetter)->setWidth($config['width']);
        }
        $sheet->getStyle("A2:{$highestColumn}2")
            ->getFont()
            ->setBold(true)
            ->setSize($styleConfig['header_font_size'])
            ->setColor(new Color($styleConfig['header_font_color']));
        $sheet->getStyle("A2:{$highestColumn}2")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($styleConfig['header_background']);
        $sheet->getStyle("A2:{$highestColumn}2")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(false);
        $sheet->getStyle("A2:{$highestColumn}2")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->setColor(new Color($styleConfig['header_border_color']));

        // 填充数据
        foreach ($exportList as $key => $user) {
            $row = $key + 3;
            foreach ($headers as $col => $config) {
                $field = $config['field'] ?? array_keys($user)[array_search($col, array_keys($headers))];
                $value = $user[$field] ?? '';
                // 如果字段有 numberFormat 配置，强制转换为浮点数
                if (isset($config['numberFormat']) && $config['numberFormat']) {
                    $value = (float)$value;
                    $sheet->setCellValue("{$col}{$row}", $value);
                } else {
                    $sheet->setCellValueExplicit("{$col}{$row}", $value, DataType::TYPE_STRING);
                }
                if ($config['monospace'] ?? false) {
                    $sheet->getStyle("{$col}{$row}")
                        ->getFont()
                        ->setName('Courier New')
                        ->setSize($styleConfig['address_font_size']);
                }
            }
        }

        // 格式化样式
        $highestRow = $sheet->getHighestRow();

        // 数据区域对齐
        $sheet->getStyle("A3:{$highestColumn}{$highestRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        // 禁用 B 列数据行的换行
        $sheet->getStyle("B3:B{$highestRow}")
            ->getAlignment()
            ->setWrapText(false);

        // 设置行背景色
        for ($row = 3; $row <= $highestRow; $row++) {
            $shouldHighlight = false;
            foreach ($headers as $col => $config) {
                $highlight = $config['highlight'];
                if (is_callable($highlight) && $highlight($exportList[$row - 3])) {
                    $shouldHighlight = true;
                    break;
                }
            }
            if ($shouldHighlight) {
                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($styleConfig['highlight_background']);
            } elseif ($row % 2 == 0) {
                $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($styleConfig['alternate_background']);
            }
        }

        // 应用数字格式
        foreach ($headers as $col => $config) {
            if (isset($config['numberFormat']) && $config['numberFormat']) {
                $sheet->getStyle("{$col}3:{$col}{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode($config['numberFormat']);
            }
        }

        // 添加边框
        $sheet->getStyle("A2:{$highestColumn}{$highestRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color($styleConfig['border_color']));

        // 总计行样式
        $sheet->getStyle("A{$highestRow}:{$highestColumn}{$highestRow}")
            ->getFont()
            ->setBold(true)
            ->setSize($styleConfig['totals_font_size'])
            ->setColor(new Color($styleConfig['totals_font_color']));
        $sheet->getStyle("A{$highestRow}:{$highestColumn}{$highestRow}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($styleConfig['totals_background']);
        $sheet->getStyle("A{$highestRow}:{$highestColumn}{$highestRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->setColor(new Color($styleConfig['totals_border_color']));

        // 自动筛选
        $sheet->setAutoFilter("A2:{$highestColumn}2");

        // 冻结窗格
        $sheet->freezePane('A3');

        // 保存文件
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = $exportFileName . '-' . date('YmdHis') . '.xlsx';
        $directoryPath = $directoryPath ?: public_path() . '/excel/';
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
        $file_path = $directoryPath . $filename;
        $writer->save($file_path);

        return [
            'filename' => $filename,
            'filepath' => $file_path,
        ];
    }

    /**
     * 根据风格名称获取风格配置
     *
     * @param string $style 风格名称
     * @return array 风格配置
     */
    private function getStyleConfig(string $style): array
    {
        $styles = [
            'modern_minimalist' => [
                'font' => 'Arial',
                'font_size' => 10,
                'font_color' => Color::COLOR_BLACK,
                'row_height' => 20,
                'title_font_size' => 12,
                'title_font_color' => Color::COLOR_BLACK,
                'title_background' => 'FFE6E6E6', // 浅灰色
                'title_row_height' => 28,
                'header_font_size' => 11,
                'header_font_color' => Color::COLOR_BLACK,
                'header_background' => 'FFE6E6E6', // 浅灰色
                'header_border_color' => Color::COLOR_BLACK,
                'address_font' => 'Courier New', // 等宽字体
                'address_font_size' => 10,
                'highlight_background' => 'FF90EE90', // 浅绿色
                'alternate_background' => 'FFE6E6E6', // 浅灰色
                'border_color' => Color::COLOR_BLACK,
                'totals_font_size' => 11,
                'totals_font_color' => Color::COLOR_BLACK,
                'totals_background' => 'FFE6E6E6', // 浅灰色
                'totals_border_color' => Color::COLOR_BLACK,
                'default_background' => 'FFFFFFFF', // 白色
            ],
            'elegant_classic' => [
                'font' => 'Times New Roman',
                'font_size' => 11,
                'font_color' => Color::COLOR_BLACK,
                'row_height' => 22,
                'title_font_size' => 14,
                'title_font_color' => 'FF4A2C00', // 深金色
                'title_background' => 'FFF5E8C7', // 浅米金色
                'title_row_height' => 30,
                'header_font_size' => 12,
                'header_font_color' => Color::COLOR_WHITE,
                'header_background' => 'FFD4A017', // 温暖金色
                'header_border_color' => 'FF8B6F47', // 浅棕色
                'address_font' => 'Courier New',
                'address_font_size' => 10,
                'highlight_background' => 'FFFFB6C1', // 浅粉红（更明显的高亮）
                'alternate_background' => 'FFF9F5E8', // 极浅米色
                'border_color' => 'FF8B6F47', // 浅棕色
                'totals_font_size' => 12,
                'totals_font_color' => 'FF4A2C00', // 深金色
                'totals_background' => 'FFF5E8C7', // 浅米金色
                'totals_border_color' => 'FF8B6F47', // 浅棕色
                'default_background' => 'FFFFFFFF', // 白色
            ],
            'vibrant_professional' => [
                'font' => 'Open Sans',
                'font_size' => 11,
                'font_color' => Color::COLOR_BLACK,
                'row_height' => 22,
                'title_font_size' => 14,
                'title_font_color' => Color::COLOR_WHITE,
                'title_background' => 'FF1976D2', // 深蓝色
                'title_row_height' => 30,
                'header_font_size' => 12,
                'header_font_color' => Color::COLOR_WHITE,
                'header_background' => 'FF42A5F5', // 浅蓝色
                'header_border_color' => 'FF90CAF9', // 更浅蓝色
                'address_font' => 'Consolas',
                'address_font_size' => 10,
                'highlight_background' => 'FF81D4FA', // 更鲜艳的浅青色（高亮更明显）
                'alternate_background' => 'FFF5F7FA', // 极浅灰蓝色
                'border_color' => 'FF90CAF9', // 浅蓝色
                'totals_font_size' => 12,
                'totals_font_color' => Color::COLOR_WHITE,
                'totals_background' => 'FF1976D2', // 深蓝色
                'totals_border_color' => 'FF90CAF9', // 浅蓝色
                'default_background' => 'FFFFFFFF', // 白色
            ],
            'soft_neutral' => [
                'font' => 'Roboto',
                'font_size' => 11,
                'font_color' => Color::COLOR_BLACK,
                'row_height' => 22,
                'title_font_size' => 14,
                'title_font_color' => 'FF37474F', // 深灰蓝色
                'title_background' => 'FFECEFF1', // 浅灰色
                'title_row_height' => 30,
                'header_font_size' => 12,
                'header_font_color' => Color::COLOR_BLACK,
                'header_background' => 'FFB0BEC5', // 浅灰蓝色
                'header_border_color' => 'FF78909C', // 中灰蓝色
                'address_font' => 'Consolas',
                'address_font_size' => 10,
                'highlight_background' => 'FF80DEEA', // 浅青色（更明显的高亮）
                'alternate_background' => 'FFF5F7FA', // 极浅灰色
                'border_color' => 'FF78909C', // 中灰蓝色
                'totals_font_size' => 12,
                'totals_font_color' => 'FF37474F', // 深灰蓝色
                'totals_background' => 'FFECEFF1', // 浅灰色
                'totals_border_color' => 'FF78909C', // 中灰蓝色
                'default_background' => 'FFFFFFFF', // 白色
            ],
            'sleek_corporate' => [
                'font' => 'Helvetica',
                'font_size' => 11,
                'font_color' => Color::COLOR_BLACK,
                'row_height' => 22,
                'title_font_size' => 14,
                'title_font_color' => Color::COLOR_WHITE,
                'title_background' => 'FF0D47A1', // 深蓝色
                'title_row_height' => 30,
                'header_font_size' => 12,
                'header_font_color' => Color::COLOR_WHITE,
                'header_background' => 'FF1565C0', // 中深蓝色
                'header_border_color' => 'FF42A5F5', // 浅蓝色
                'address_font' => 'Consolas',
                'address_font_size' => 10,
                'highlight_background' => 'FF64B5F6', // 更鲜艳的浅蓝色（高亮更明显）
                'alternate_background' => 'FFE3F2FD', // 极浅蓝色
                'border_color' => 'FF42A5F5', // 浅蓝色
                'totals_font_size' => 12,
                'totals_font_color' => Color::COLOR_WHITE,
                'totals_background' => 'FF0D47A1', // 深蓝色
                'totals_border_color' => 'FF42A5F5', // 浅蓝色
                'default_background' => 'FFFFFFFF', // 白色
            ],
            'pastel_serenity' => [
                'font' => 'Lato',
                'font_size' => 11,
                'font_color' => Color::COLOR_BLACK,
                'row_height' => 22,
                'title_font_size' => 14,
                'title_font_color' => 'FF4A3C6A', // 深紫色
                'title_background' => 'FFEDE7F6', // 极浅紫色
                'title_row_height' => 30,
                'header_font_size' => 12,
                'header_font_color' => Color::COLOR_WHITE,
                'header_background' => 'FFB39DDB', // 淡紫色
                'header_border_color' => 'FF9575CD', // 中紫色
                'address_font' => 'Consolas',
                'address_font_size' => 10,
                'highlight_background' => 'FFCE93D8', // 更鲜艳的浅紫色（高亮更明显）
                'alternate_background' => 'FFF5F5F5', // 极浅灰色
                'border_color' => 'FF9575CD', // 中紫色
                'totals_font_size' => 12,
                'totals_font_color' => 'FF4A3C6A', // 深紫色
                'totals_background' => 'FFEDE7F6', // 极浅紫色
                'totals_border_color' => 'FF9575CD', // 中紫色
                'default_background' => 'FFFFFFFF', // 白色
            ],
        ];

        return $styles[$style] ?? $styles['modern_minimalist'];
    }

    /**
     * 将列索引转换为字母（例如 0 => A, 1 => B, 25 => Z, 26 => AA）
     *
     * @param int $index 列索引
     * @return string 列字母
     */
    private function getColumnLetter(int $index): string
    {
        $letters = '';
        while ($index >= 0) {
            $letters = chr(65 + ($index % 26)) . $letters;
            $index = (int)($index / 26) - 1;
        }
        return $letters;
    }
}