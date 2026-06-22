import { Form, router } from '@inertiajs/react'
import { useState } from 'react'
import * as FactoryController from '@/actions/App/Http/Controllers/FactoryController'
import * as UpgradeController from '@/actions/App/Http/Controllers/UpgradeController'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import type { FactoryLineData, ResourceNodeData } from '@/types/game'
import UpgradeButton from './UpgradeButton'

interface Props {
    lines: FactoryLineData[]
    nodes: ResourceNodeData[]
    money: number
}

const statusColors: Record<string, string> = {
    idle: 'bg-secondary text-secondary-foreground',
    running: 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
    broken: 'bg-destructive/20 text-destructive',
}

function canAffordQueue(line: FactoryLineData, nodes: ResourceNodeData[], count: number): boolean {
    return Object.entries(line.materialsRequired).every(([type, perCar]) => {
        const node = nodes.find((n) => n.type === type)

        return node !== undefined && node.amount >= perCar * count
    })
}

function FactoryLine({ line, nodes, money }: { line: FactoryLineData; nodes: ResourceNodeData[]; money: number }) {
    const [queueInput, setQueueInput] = useState('1')
    const count = Math.max(1, parseInt(queueInput) || 1)
    const affordable = canAffordQueue(line, nodes, count)

    const materialsLabel = Object.entries(line.materialsRequired)
        .map(([type, amount]) => `${amount * count} ${type}`)
        .join(', ')

    return (
        <div className="flex flex-col gap-3">
            <div className="flex items-center justify-between">
                <span className="font-medium">{line.name} — {line.carModelLabel}</span>
                <Badge className={statusColors[line.status]} variant="outline">
                    {line.status}
                </Badge>
            </div>

            <div className="h-2 w-full overflow-hidden rounded-full bg-secondary">
                <div
                    className="h-full rounded-full bg-blue-500 transition-all duration-300"
                    style={{ width: `${line.progressPercent}%` }}
                />
            </div>

            <div className="grid grid-cols-3 gap-2 text-xs text-muted-foreground">
                <div className="text-center">
                    <div className="font-mono text-foreground">{line.queueCount}</div>
                    <div>in queue</div>
                </div>
                <div className="text-center">
                    <div className="font-mono text-foreground">{line.productionTimeSeconds.toFixed(0)}s</div>
                    <div>per car</div>
                </div>
                <div className="text-center">
                    <div className="font-mono text-emerald-600 dark:text-emerald-400">{line.completedBuffer}</div>
                    <div>ready</div>
                </div>
            </div>

            <Form
                action={FactoryController.queue(line.id).url}
                method="post"
                className="flex items-center gap-2"
            >
                {({ processing }) => (
                    <>
                        <Input
                            type="number"
                            name="count"
                            value={queueInput}
                            onChange={(e) => setQueueInput(e.target.value)}
                            min={1}
                            max={100}
                            className="h-8 w-20 text-center font-mono text-sm"
                        />
                        <Button
                            type="submit"
                            size="sm"
                            disabled={processing || !affordable}
                            className="flex-1 text-xs"
                        >
                            Queue ({materialsLabel})
                        </Button>
                    </>
                )}
            </Form>

            <div className="flex gap-1.5">
                <Button
                    variant={line.qualityMode ? 'default' : 'outline'}
                    size="sm"
                    className="flex-1 text-xs"
                    onClick={() => router.post(FactoryController.toggleQuality(line.id).url)}
                >
                    {line.qualityMode ? '✦ Quality ON' : '◇ Quality OFF'}
                </Button>
            </div>

            <UpgradeButton
                href={UpgradeController.factorySpeed(line.id).url}
                label={`Line Speed (Lv${line.speedLevel} → ${line.speedLevel + 1})`}
                cost={line.upgradeSpeedCost}
                money={money}
            />
        </div>
    )
}

export default function FactoryPanel({ lines, nodes, money }: Props) {
    return (
        <Card className="flex flex-col gap-4 py-4">
            <CardHeader className="px-4 pb-0">
                <CardTitle className="flex items-center gap-2 text-base">
                    <span>🏭</span> Factory
                </CardTitle>
            </CardHeader>
            <CardContent className="flex flex-col gap-4 px-4">
                {lines.map((line) => (
                    <FactoryLine key={line.id} line={line} nodes={nodes} money={money} />
                ))}
            </CardContent>
        </Card>
    )
}
