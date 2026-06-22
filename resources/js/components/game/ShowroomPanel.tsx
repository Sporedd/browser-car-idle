import { Form, router } from '@inertiajs/react'
import * as ShowroomController from '@/actions/App/Http/Controllers/ShowroomController'
import * as UpgradeController from '@/actions/App/Http/Controllers/UpgradeController'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import type { ShowroomData, ShowroomInventoryData } from '@/types/game'
import UpgradeButton, { formatMoney } from './UpgradeButton'

interface Props {
    showrooms: ShowroomData[]
    money: number
}

function InventoryRow({ item }: { item: ShowroomInventoryData }) {
    const isPriceModified = item.priceOverride !== null
    const priceRatio = item.effectivePrice / item.basePrice
    const priceColor =
        priceRatio > 1.1
            ? 'text-emerald-600 dark:text-emerald-400'
            : priceRatio < 0.9
              ? 'text-red-500'
              : 'text-foreground'

    return (
        <div className="flex flex-col gap-2 rounded-md border p-3">
            <div className="flex items-center justify-between">
                <span className="font-medium text-sm">{item.carModelLabel}</span>
                <div className="flex items-center gap-2">
                    <span className="font-mono text-sm">{item.quantity}</span>
                    <span className="text-xs text-muted-foreground">in stock</span>
                </div>
            </div>

            <div className="flex items-center justify-between text-xs text-muted-foreground">
                <span>
                    Sales:{' '}
                    <span className="font-mono text-emerald-600 dark:text-emerald-400">
                        {item.salesRatePerMinute.toFixed(2)}/min
                    </span>
                </span>
                <span>
                    Price:{' '}
                    <span className={`font-mono ${priceColor}`}>{formatMoney(item.effectivePrice)}</span>
                    {isPriceModified && (
                        <span className="ml-1 text-muted-foreground">(base {formatMoney(item.basePrice)})</span>
                    )}
                </span>
            </div>

            <Form
                action={ShowroomController.setPrice(item.id).url}
                method="post"
                className="flex items-center gap-2"
            >
                {({ processing }) => (
                    <>
                        <Input
                            type="number"
                            name="price"
                            defaultValue={item.effectivePrice}
                            min={1}
                            step={10}
                            className="h-7 flex-1 text-xs font-mono"
                        />
                        <Button type="submit" size="sm" disabled={processing} className="h-7 text-xs px-2">
                            Set
                        </Button>
                        {isPriceModified && (
                            <Button
                                variant="ghost"
                                size="sm"
                                className="h-7 text-xs px-2"
                                onClick={() => router.post(ShowroomController.resetPrice(item.id).url)}
                            >
                                Reset
                            </Button>
                        )}
                    </>
                )}
            </Form>
        </div>
    )
}

function ShowroomCard({ showroom, money }: { showroom: ShowroomData; money: number }) {
    return (
        <div className="flex flex-col gap-3">
            <div className="flex items-center justify-between">
                <span className="font-medium">{showroom.name}</span>
                <Badge variant="outline" className="text-xs capitalize">
                    {showroom.locationTier}
                </Badge>
            </div>

            <div className="grid grid-cols-2 gap-2 text-xs text-muted-foreground">
                <div>
                    Staff <span className="font-mono text-foreground">Lv{showroom.staffLevel}</span>
                </div>
                <div>
                    Marketing <span className="font-mono text-foreground">Lv{showroom.marketingLevel}</span>
                </div>
                <div className="col-span-2">
                    Demand multiplier:{' '}
                    <span className="font-mono text-emerald-600 dark:text-emerald-400">
                        ×{showroom.demandMultiplier.toFixed(2)}
                    </span>
                </div>
            </div>

            {showroom.inventory.length > 0 ? (
                <div className="flex flex-col gap-2">
                    {showroom.inventory.map((item) => (
                        <InventoryRow key={item.id} item={item} />
                    ))}
                </div>
            ) : (
                <div className="rounded-md border border-dashed p-4 text-center text-xs text-muted-foreground">
                    No cars in stock — dispatch a truck to deliver some.
                </div>
            )}

            <div className="flex flex-col gap-1.5">
                <UpgradeButton
                    href={UpgradeController.showroomStaff(showroom.id).url}
                    label={`Staff (Lv${showroom.staffLevel} → ${showroom.staffLevel + 1})`}
                    cost={showroom.upgradeStaffCost}
                    money={money}
                />
                <UpgradeButton
                    href={UpgradeController.showroomMarketing(showroom.id).url}
                    label={`Marketing (Lv${showroom.marketingLevel} → ${showroom.marketingLevel + 1})`}
                    cost={showroom.upgradeMarketingCost}
                    money={money}
                />
            </div>
        </div>
    )
}

export default function ShowroomPanel({ showrooms, money }: Props) {
    return (
        <Card className="flex flex-col gap-4 py-4">
            <CardHeader className="px-4 pb-0">
                <CardTitle className="flex items-center gap-2 text-base">
                    <span>🏬</span> Showrooms
                </CardTitle>
            </CardHeader>
            <CardContent className="flex flex-col gap-4 px-4">
                {showrooms.map((s) => (
                    <ShowroomCard key={s.id} showroom={s} money={money} />
                ))}
            </CardContent>
        </Card>
    )
}
