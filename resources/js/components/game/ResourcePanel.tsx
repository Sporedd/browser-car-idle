import * as UpgradeController from '@/actions/App/Http/Controllers/UpgradeController'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import type { ResourceNodeData } from '@/types/game'
import UpgradeButton from './UpgradeButton'

interface Props {
    nodes: ResourceNodeData[]
    money: number
}

export default function ResourcePanel({ nodes, money }: Props) {
    return (
        <Card className="flex flex-col gap-4 py-4">
            <CardHeader className="px-4 pb-0">
                <CardTitle className="flex items-center gap-2 text-base">
                    <span>⛏</span> Resources
                </CardTitle>
            </CardHeader>
            <CardContent className="flex flex-col gap-4 px-4">
                {nodes.map((node) => (
                    <div key={node.id} className="flex flex-col gap-3">
                        <div className="flex items-center justify-between">
                            <span className="font-medium">{node.label}</span>
                            <span className="text-sm text-muted-foreground">
                                {node.amount.toFixed(0)} / {node.capacity.toFixed(0)}
                            </span>
                        </div>

                        <div className="h-2 w-full overflow-hidden rounded-full bg-secondary">
                            <div
                                className="h-full rounded-full bg-amber-500 transition-all duration-500"
                                style={{ width: `${Math.min(100, (node.amount / node.capacity) * 100)}%` }}
                            />
                        </div>

                        <div className="flex items-center justify-between text-xs text-muted-foreground">
                            <span>Production</span>
                            <span className="font-mono text-emerald-600 dark:text-emerald-400">
                                +{node.productionRate.toFixed(1)}/s
                            </span>
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <UpgradeButton
                                href={UpgradeController.mineSpeed(node.id).url}
                                label={`Mine Speed (Lv${node.speedLevel} → ${node.speedLevel + 1})`}
                                cost={node.upgradeSpeedCost}
                                money={money}
                            />
                            <UpgradeButton
                                href={UpgradeController.mineStorage(node.id).url}
                                label={`Storage (Lv${node.storageLevel} → ${node.storageLevel + 1})`}
                                cost={node.upgradeStorageCost}
                                money={money}
                            />
                        </div>
                    </div>
                ))}
            </CardContent>
        </Card>
    )
}
