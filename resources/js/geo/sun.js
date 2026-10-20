/*
 * Solar position and the day's light windows, from the low-precision
 * formulas in Meeus' Astronomical Algorithms (the same model SunCalc uses).
 * Good to about a minute for times and a fraction of a degree for position,
 * which is finer than refraction near the horizon lets anyone use.
 */

const RAD = Math.PI / 180
const DAY_MS = 86400000
const J1970 = 2440588
const J2000 = 2451545
const OBLIQUITY = RAD * 23.4397
const J0 = 0.0009

const toDays = (date) => date.valueOf() / DAY_MS - 0.5 + J1970 - J2000
const fromJulian = (julian) => new Date((julian + 0.5 - J1970) * DAY_MS)

const meanAnomaly = (days) => RAD * (357.5291 + 0.98560028 * days)

function eclipticLongitude(anomaly) {
    const center = RAD * (1.9148 * Math.sin(anomaly) + 0.02 * Math.sin(2 * anomaly) + 0.0003 * Math.sin(3 * anomaly))

    return anomaly + center + RAD * 102.9372 + Math.PI
}

const declination = (longitude) => Math.asin(Math.sin(OBLIQUITY) * Math.sin(longitude))
const rightAscension = (longitude) => Math.atan2(Math.sin(longitude) * Math.cos(OBLIQUITY), Math.cos(longitude))

/**
 * Where the sun is right now.
 *
 * @returns {{ azimuth: number, altitude: number }} Degrees; azimuth is a
 *     compass bearing (0 = north, 90 = east).
 */
export function sunPosition(date, lat, lng) {
    const phi = RAD * lat
    const days = toDays(date)
    const longitude = eclipticLongitude(meanAnomaly(days))
    const dec = declination(longitude)
    const hourAngle = RAD * (280.16 + 360.9856235 * days) + RAD * lng - rightAscension(longitude)

    const altitude = Math.asin(Math.sin(phi) * Math.sin(dec) + Math.cos(phi) * Math.cos(dec) * Math.cos(hourAngle))
    const fromSouth = Math.atan2(Math.sin(hourAngle), Math.cos(hourAngle) * Math.sin(phi) - Math.tan(dec) * Math.cos(phi))

    return {
        azimuth: (fromSouth / RAD + 180 + 360) % 360,
        altitude: altitude / RAD,
    }
}

/*
 * Sun elevations that bound each window. Golden and blue hour follow the
 * photographers' convention (−4° to 6° and −6° to −4°); sunrise and sunset
 * use −0.833° for refraction and the sun's radius.
 */
const ELEVATIONS = {
    blueStart: -6,
    goldenStart: -4,
    sunrise: -0.833,
    goldenEnd: 6,
}

/**
 * The solar day for the calendar date at that longitude. Each value is a
 * Date, or null when the sun never crosses that elevation (polar day or
 * night).
 *
 * @returns {{ solarNoon: Date, dawnBlue: Date|null, dawnGolden: Date|null,
 *     sunrise: Date|null, morningGoldenEnd: Date|null,
 *     eveningGoldenStart: Date|null, sunset: Date|null,
 *     duskGolden: Date|null, duskBlue: Date|null }}
 */
export function sunTimes(date, lat, lng) {
    const phi = RAD * lat
    const westLongitude = RAD * -lng
    const days = toDays(localSolarNoonEstimate(date, lng))
    const cycle = Math.round(days - J0 - westLongitude / (2 * Math.PI))
    const approxNoon = J0 + westLongitude / (2 * Math.PI) + cycle
    const anomaly = meanAnomaly(approxNoon)
    const longitude = eclipticLongitude(anomaly)
    const dec = declination(longitude)
    const noon = J2000 + approxNoon + 0.0053 * Math.sin(anomaly) - 0.0069 * Math.sin(2 * longitude)

    // Julian date of the evening crossing of an elevation; the morning one
    // mirrors it about solar noon.
    const evening = (elevation) => {
        const cosHour = (Math.sin(RAD * elevation) - Math.sin(phi) * Math.sin(dec)) / (Math.cos(phi) * Math.cos(dec))

        if (cosHour < -1 || cosHour > 1) {
            return null
        }

        const hour = Math.acos(cosHour)
        const transit = J0 + (hour + westLongitude) / (2 * Math.PI) + cycle

        return J2000 + transit + 0.0053 * Math.sin(anomaly) - 0.0069 * Math.sin(2 * longitude)
    }

    const pair = (elevation) => {
        const set = evening(elevation)

        return set === null
            ? [null, null]
            : [fromJulian(noon - (set - noon)), fromJulian(set)]
    }

    const [dawnBlue, duskBlue] = pair(ELEVATIONS.blueStart)
    const [dawnGolden, duskGolden] = pair(ELEVATIONS.goldenStart)
    const [sunrise, sunset] = pair(ELEVATIONS.sunrise)
    const [morningGoldenEnd, eveningGoldenStart] = pair(ELEVATIONS.goldenEnd)

    return {
        solarNoon: fromJulian(noon),
        dawnBlue,
        dawnGolden,
        sunrise,
        morningGoldenEnd,
        eveningGoldenStart,
        sunset,
        duskGolden,
        duskBlue,
    }
}

/*
 * Noon of the calendar day as lived at that longitude, not in the device's
 * time zone: someone in Orlando checking Tokyo at 11 pm wants Tokyo's today.
 */
function localSolarNoonEstimate(date, lng) {
    const offsetMs = (lng / 15) * 3600000
    const local = new Date(date.valueOf() + offsetMs)

    return new Date(Date.UTC(local.getUTCFullYear(), local.getUTCMonth(), local.getUTCDate(), 12) - offsetMs)
}

const COMPASS_POINTS = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW']

export function compassPoint(azimuth) {
    return COMPASS_POINTS[Math.round(azimuth / 22.5) % 16]
}

/** The sun's track across the sky today, sampled every 15 minutes while it is up. */
export function sunPath(times, lat, lng) {
    if (times.sunrise === null || times.sunset === null) {
        return []
    }

    const points = []

    for (let at = times.sunrise.valueOf(); at <= times.sunset.valueOf(); at += 15 * 60000) {
        points.push(sunPosition(new Date(at), lat, lng))
    }

    points.push(sunPosition(times.sunset, lat, lng))

    return points
}
